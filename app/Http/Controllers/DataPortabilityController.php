<?php

namespace App\Http\Controllers;

use App\Models\CardioSession;
use App\Models\Exercise;
use App\Models\ExerciseSet;
use App\Models\Location;
use App\Models\TrainingPlan;
use App\Models\WorkoutSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DataPortabilityController extends Controller
{
    public function index()
    {
        return view('data.portability');
    }

    // ── Export ─────────────────────────────────────────────────────────────

    public function exportWorkouts()
    {
        $userId = Auth::id();

        $sets = ExerciseSet::query()
            ->join('workout_sessions', 'exercise_sets.workout_session_id', '=', 'workout_sessions.id')
            ->join('exercises',        'workout_sessions.exercise_id',      '=', 'exercises.id')
            ->join('training_plans',   'exercises.training_plan_id',        '=', 'training_plans.id')
            ->join('locations',        'training_plans.location_id',        '=', 'locations.id')
            ->where('locations.user_id', $userId)
            ->whereNull('workout_sessions.deleted_at')
            ->orderBy('workout_sessions.logged_at')
            ->orderBy('workout_sessions.id')
            ->orderBy('exercise_sets.set_number')
            ->select([
                'workout_sessions.logged_at',
                'locations.name as location',
                'training_plans.name as training_plan',
                'exercises.name as exercise',
                'exercises.is_unilateral',
                'exercise_sets.set_number',
                'exercise_sets.weight',
                'exercise_sets.reps',
                'exercise_sets.reps_left',
                'exercise_sets.reps_right',
            ])
            ->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="gymmate_workouts.csv"',
        ];

        $callback = function () use ($sets) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM for Excel
            fputcsv($out, ['logged_at', 'location', 'training_plan', 'exercise',
                           'is_unilateral', 'set_number', 'weight', 'reps', 'reps_left', 'reps_right']);
            foreach ($sets as $row) {
                fputcsv($out, [
                    $row->logged_at,
                    $row->location,
                    $row->training_plan,
                    $row->exercise,
                    $row->is_unilateral ? 1 : 0,
                    $row->set_number,
                    $row->weight,
                    $row->reps,
                    $row->reps_left,
                    $row->reps_right,
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportCardio()
    {
        $sessions = CardioSession::where('user_id', Auth::id())
            ->orderBy('logged_at')
            ->get();

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="gymmate_cardio.csv"',
        ];

        $callback = function () use ($sessions) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($out, ['logged_at', 'activity', 'duration_minutes', 'distance_km',
                           'intensity', 'hiit_rounds', 'hiit_work_seconds', 'hiit_rest_seconds', 'notes']);
            foreach ($sessions as $s) {
                fputcsv($out, [
                    $s->logged_at,
                    $s->activity,
                    $s->duration_minutes,
                    $s->distance_km,
                    $s->intensity,
                    $s->hiit_rounds,
                    $s->hiit_work_seconds,
                    $s->hiit_rest_seconds,
                    $s->notes,
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ── Import ─────────────────────────────────────────────────────────────

    public function importWorkouts(Request $request)
    {
        $request->validate(['file' => ['required', 'file', 'mimes:csv,txt', 'max:5120']]);

        $path    = $request->file('file')->getRealPath();
        $handle  = fopen($path, 'r');
        $header  = fgetcsv($handle);

        if ($header === false || count($header) < 10) {
            return back()->withErrors(['file' => 'Ungültiges CSV-Format.']);
        }

        // strip UTF-8 BOM if present
        $header[0] = ltrim($header[0], "\xEF\xBB\xBF");

        $expected = ['logged_at', 'location', 'training_plan', 'exercise',
                     'is_unilateral', 'set_number', 'weight', 'reps', 'reps_left', 'reps_right'];

        if ($header !== $expected) {
            return back()->withErrors(['file' => 'CSV-Spalten passen nicht. Erwartet: ' . implode(', ', $expected)]);
        }

        $userId          = Auth::id();
        $locationCache   = [];
        $planCache       = [];
        $exerciseCache   = [];
        $sessionCache    = [];
        $importedSets    = 0;

        DB::transaction(function () use (
            $handle, $userId,
            &$locationCache, &$planCache, &$exerciseCache, &$sessionCache, &$importedSets
        ) {
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) < 10) continue;

                [$loggedAt, $locationName, $planName, $exerciseName,
                 $isUnilateral, $setNumber, $weight, $reps, $repsLeft, $repsRight] = $row;

                if (empty($loggedAt) || empty($locationName) || empty($exerciseName)) continue;

                // Find or create location
                $locKey = $userId . '|' . $locationName;
                if (!isset($locationCache[$locKey])) {
                    $locationCache[$locKey] = Location::firstOrCreate(
                        ['user_id' => $userId, 'name' => $locationName]
                    )->id;
                }

                // Find or create training plan
                $planKey = $locationCache[$locKey] . '|' . $planName;
                if (!isset($planCache[$planKey])) {
                    $planCache[$planKey] = TrainingPlan::firstOrCreate(
                        ['location_id' => $locationCache[$locKey], 'name' => $planName]
                    )->id;
                }

                // Find or create exercise
                $exKey = $planCache[$planKey] . '|' . $exerciseName;
                if (!isset($exerciseCache[$exKey])) {
                    $exerciseCache[$exKey] = Exercise::firstOrCreate(
                        ['training_plan_id' => $planCache[$planKey], 'name' => $exerciseName],
                        ['is_unilateral' => (bool) $isUnilateral]
                    )->id;
                }

                // Find or create workout session (keyed by exercise + timestamp)
                $sessionKey = $exerciseCache[$exKey] . '|' . $loggedAt;
                if (!isset($sessionCache[$sessionKey])) {
                    $sessionCache[$sessionKey] = WorkoutSession::firstOrCreate([
                        'exercise_id' => $exerciseCache[$exKey],
                        'logged_at'   => $loggedAt,
                    ])->id;
                }

                ExerciseSet::create([
                    'workout_session_id' => $sessionCache[$sessionKey],
                    'set_number'         => (int) $setNumber,
                    'weight'             => $weight !== '' ? (float) $weight : null,
                    'reps'               => $reps !== '' ? (int) $reps : null,
                    'reps_left'          => $repsLeft !== '' ? (int) $repsLeft : null,
                    'reps_right'         => $repsRight !== '' ? (int) $repsRight : null,
                ]);

                $importedSets++;
            }
        });

        fclose($handle);

        return back()->with('success_workouts', "{$importedSets} Sätze erfolgreich importiert.");
    }

    public function importCardio(Request $request)
    {
        $request->validate(['file' => ['required', 'file', 'mimes:csv,txt', 'max:5120']]);

        $path   = $request->file('file')->getRealPath();
        $handle = fopen($path, 'r');
        $header = fgetcsv($handle);

        if ($header === false || count($header) < 9) {
            return back()->withErrors(['file' => 'Ungültiges CSV-Format.']);
        }

        $header[0] = ltrim($header[0], "\xEF\xBB\xBF");

        $expected = ['logged_at', 'activity', 'duration_minutes', 'distance_km',
                     'intensity', 'hiit_rounds', 'hiit_work_seconds', 'hiit_rest_seconds', 'notes'];

        if ($header !== $expected) {
            return back()->withErrors(['file' => 'CSV-Spalten passen nicht. Erwartet: ' . implode(', ', $expected)]);
        }

        $userId  = Auth::id();
        $count   = 0;

        DB::transaction(function () use ($handle, $userId, &$count) {
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) < 9) continue;

                [$loggedAt, $activity, $duration, $distance,
                 $intensity, $hiitRounds, $hiitWork, $hiitRest, $notes] = $row;

                if (empty($loggedAt) || empty($activity) || $duration === '') continue;

                CardioSession::create([
                    'user_id'           => $userId,
                    'logged_at'         => $loggedAt,
                    'activity'          => $activity,
                    'duration_minutes'  => (int) $duration,
                    'distance_km'       => $distance !== '' ? (float) $distance : null,
                    'intensity'         => $intensity ?: 'mittel',
                    'hiit_rounds'       => $hiitRounds !== '' ? (int) $hiitRounds : null,
                    'hiit_work_seconds' => $hiitWork !== '' ? (int) $hiitWork : null,
                    'hiit_rest_seconds' => $hiitRest !== '' ? (int) $hiitRest : null,
                    'notes'             => $notes ?: null,
                ]);

                $count++;
            }
        });

        fclose($handle);

        return back()->with('success_cardio', "{$count} Cardio-Sessions erfolgreich importiert.");
    }
}
