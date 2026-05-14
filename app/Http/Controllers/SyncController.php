<?php

namespace App\Http\Controllers;

use App\Models\CardioSession;
use App\Models\Exercise;
use App\Models\Location;
use App\Models\TrainingPlan;
use App\Models\WeeklySchedule;
use App\Models\WorkoutSession;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SyncController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        $request->validate([
            'type'      => ['required', 'string'],
            'payload'   => ['required', 'array'],
            'queued_at' => ['required', 'string'],
        ]);

        $type      = $request->input('type');
        $payload   = $request->input('payload');
        $queuedAt  = Carbon::parse($request->input('queued_at'));
        $userId    = Auth::id();

        return match ($type) {
            'log_workout'       => $this->logWorkout($payload, $userId),
            'delete_workout'    => $this->deleteWorkout($payload, $userId),
            'log_cardio'        => $this->logCardio($payload, $userId),
            'delete_cardio'     => $this->deleteCardio($payload, $userId),
            'create_location'   => $this->createLocation($payload, $userId),
            'update_location'   => $this->updateLocation($payload, $userId, $queuedAt),
            'delete_location'   => $this->deleteLocation($payload, $userId),
            'create_plan'       => $this->createPlan($payload, $userId),
            'update_plan'       => $this->updatePlan($payload, $userId, $queuedAt),
            'delete_plan'       => $this->deletePlan($payload, $userId),
            'create_exercise'   => $this->createExercise($payload, $userId),
            'update_exercise'   => $this->updateExercise($payload, $userId, $queuedAt),
            'delete_exercise'   => $this->deleteExercise($payload, $userId),
            'update_schedule'   => $this->updateSchedule($payload, $userId, $queuedAt),
            default             => response()->json(['success' => false, 'message' => 'Unknown type'], 422),
        };
    }

    private function logWorkout(array $p, int $userId): JsonResponse
    {
        $exercise = Exercise::find($p['exercise_id'] ?? null);
        if (!$exercise || $exercise->trainingPlan->location->user_id !== $userId) {
            return response()->json(['success' => false, 'message' => 'Exercise not found'], 404);
        }

        $session = WorkoutSession::create([
            'exercise_id' => $exercise->id,
            'logged_at'   => $p['logged_at'],
        ]);

        foreach ($p['sets'] ?? [] as $i => $set) {
            $session->sets()->create([
                'set_number' => $i + 1,
                'weight'     => $set['weight'],
                'reps'       => $exercise->is_unilateral ? null : ($set['reps'] ?? null),
                'reps_left'  => $exercise->is_unilateral ? ($set['reps_left'] ?? null) : null,
                'reps_right' => $exercise->is_unilateral ? ($set['reps_right'] ?? null) : null,
            ]);
        }

        return response()->json(['success' => true]);
    }

    private function deleteWorkout(array $p, int $userId): JsonResponse
    {
        $session = WorkoutSession::find($p['session_id'] ?? null);
        if (!$session) {
            return response()->json(['success' => true]); // already gone
        }
        if ($session->exercise->trainingPlan->location->user_id !== $userId) {
            return response()->json(['success' => false], 403);
        }
        $session->delete();
        return response()->json(['success' => true]);
    }

    private function logCardio(array $p, int $userId): JsonResponse
    {
        $user = Auth::user();
        $durationMinutes = isset($p['hiit_rounds'], $p['hiit_work_seconds'], $p['hiit_rest_seconds']) && !($p['duration_minutes'] ?? null)
            ? (int) round(($p['hiit_rounds'] * ($p['hiit_work_seconds'] + $p['hiit_rest_seconds'])) / 60)
            : (int) ($p['duration_minutes'] ?? 1);

        $calories = ($user->weight_kg !== null)
            ? CardioSession::calculateCalories(
                $p['activity'],
                $p['intensity'] ?? 'mittel',
                max(1, $durationMinutes),
                (float) $user->weight_kg
            )
            : null;

        CardioSession::create([
            'user_id'           => $userId,
            'activity'          => $p['activity'],
            'duration_minutes'  => max(1, $durationMinutes),
            'distance_km'       => $p['distance_km'] ?? null,
            'intensity'         => $p['intensity'] ?? 'mittel',
            'hiit_rounds'       => $p['hiit_rounds'] ?? null,
            'hiit_work_seconds' => $p['hiit_work_seconds'] ?? null,
            'hiit_rest_seconds' => $p['hiit_rest_seconds'] ?? null,
            'notes'             => $p['notes'] ?? null,
            'calories_burned'   => $calories,
            'logged_at'         => $p['logged_at'],
        ]);

        return response()->json(['success' => true]);
    }

    private function deleteCardio(array $p, int $userId): JsonResponse
    {
        $session = CardioSession::find($p['cardio_id'] ?? null);
        if (!$session) {
            return response()->json(['success' => true]);
        }
        if ($session->user_id !== $userId) {
            return response()->json(['success' => false], 403);
        }
        $session->delete();
        return response()->json(['success' => true]);
    }

    private function createLocation(array $p, int $userId): JsonResponse
    {
        Location::create(['user_id' => $userId, 'name' => $p['name']]);
        return response()->json(['success' => true]);
    }

    private function updateLocation(array $p, int $userId, Carbon $queuedAt): JsonResponse
    {
        $location = Location::find($p['location_id'] ?? null);
        if (!$location || $location->user_id !== $userId) {
            return response()->json(['success' => false], 404);
        }

        if ($location->updated_at > $queuedAt) {
            return response()->json(['success' => true, 'conflict' => true,
                'message' => 'Eine neuere Version wurde von einem anderen Gerät gespeichert']);
        }

        $location->update(['name' => $p['name']]);
        return response()->json(['success' => true]);
    }

    private function deleteLocation(array $p, int $userId): JsonResponse
    {
        $location = Location::find($p['location_id'] ?? null);
        if (!$location) {
            return response()->json(['success' => true]);
        }
        if ($location->user_id !== $userId) {
            return response()->json(['success' => false], 403);
        }
        $location->delete();
        return response()->json(['success' => true]);
    }

    private function createPlan(array $p, int $userId): JsonResponse
    {
        $location = Location::find($p['location_id'] ?? null);
        if (!$location || $location->user_id !== $userId) {
            return response()->json(['success' => false], 404);
        }
        $location->trainingPlans()->create(['name' => $p['name']]);
        return response()->json(['success' => true]);
    }

    private function updatePlan(array $p, int $userId, Carbon $queuedAt): JsonResponse
    {
        $plan = TrainingPlan::find($p['plan_id'] ?? null);
        if (!$plan || $plan->location->user_id !== $userId) {
            return response()->json(['success' => false], 404);
        }

        if ($plan->updated_at > $queuedAt) {
            return response()->json(['success' => true, 'conflict' => true,
                'message' => 'Eine neuere Version wurde von einem anderen Gerät gespeichert']);
        }

        $plan->update(['name' => $p['name']]);
        return response()->json(['success' => true]);
    }

    private function deletePlan(array $p, int $userId): JsonResponse
    {
        $plan = TrainingPlan::find($p['plan_id'] ?? null);
        if (!$plan) {
            return response()->json(['success' => true]);
        }
        if ($plan->location->user_id !== $userId) {
            return response()->json(['success' => false], 403);
        }
        $plan->delete();
        return response()->json(['success' => true]);
    }

    private function createExercise(array $p, int $userId): JsonResponse
    {
        $plan = TrainingPlan::find($p['plan_id'] ?? null);
        if (!$plan || $plan->location->user_id !== $userId) {
            return response()->json(['success' => false], 404);
        }
        $plan->exercises()->create([
            'name'          => $p['name'],
            'description'   => $p['description'] ?? null,
            'is_unilateral' => (bool) ($p['is_unilateral'] ?? false),
        ]);
        return response()->json(['success' => true]);
    }

    private function updateExercise(array $p, int $userId, Carbon $queuedAt): JsonResponse
    {
        $exercise = Exercise::find($p['exercise_id'] ?? null);
        if (!$exercise || $exercise->trainingPlan->location->user_id !== $userId) {
            return response()->json(['success' => false], 404);
        }

        if ($exercise->updated_at > $queuedAt) {
            return response()->json(['success' => true, 'conflict' => true,
                'message' => 'Eine neuere Version wurde von einem anderen Gerät gespeichert']);
        }

        $exercise->update([
            'name'          => $p['name'],
            'description'   => $p['description'] ?? null,
            'is_unilateral' => (bool) ($p['is_unilateral'] ?? false),
        ]);
        return response()->json(['success' => true]);
    }

    private function deleteExercise(array $p, int $userId): JsonResponse
    {
        $exercise = Exercise::find($p['exercise_id'] ?? null);
        if (!$exercise) {
            return response()->json(['success' => true]);
        }
        if ($exercise->trainingPlan->location->user_id !== $userId) {
            return response()->json(['success' => false], 403);
        }
        $exercise->delete();
        return response()->json(['success' => true]);
    }

    private function updateSchedule(array $p, int $userId, Carbon $queuedAt): JsonResponse
    {
        $conflict = false;

        $userExerciseIds = Exercise::whereHas('trainingPlan.location', fn($q) => $q->where('user_id', $userId))
            ->pluck('id');

        foreach ($p['days'] ?? [] as $dayOfWeek => $values) {
            $schedule = WeeklySchedule::firstOrCreate(
                ['user_id' => $userId, 'day_of_week' => $dayOfWeek]
            );

            if ($schedule->updated_at > $queuedAt && $schedule->wasRecentlyCreated === false) {
                $conflict = true;
                continue;
            }

            $schedule->update(['is_rest' => (bool) ($values['is_rest'] ?? false)]);

            $exerciseIds = collect($values['exercises'] ?? [])
                ->filter(fn($id) => $userExerciseIds->contains((int) $id))
                ->values();

            $schedule->exercises()->sync($exerciseIds);
        }

        if (isset($p['target_reps'])) {
            Auth::user()->update(['target_reps' => (int) $p['target_reps']]);
        }

        return response()->json([
            'success'  => true,
            'conflict' => $conflict,
            'message'  => $conflict ? 'Einige Tage wurden von einem anderen Gerät geändert' : null,
        ]);
    }
}
