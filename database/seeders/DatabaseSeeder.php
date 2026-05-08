<?php

namespace Database\Seeders;

use App\Models\Exercise;
use App\Models\ExerciseSet;
use App\Models\Location;
use App\Models\TrainingPlan;
use App\Models\User;
use App\Models\WeeklySchedule;
use App\Models\WorkoutSession;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'demo@gymmate.app'],
            ['name' => 'demo', 'password' => Hash::make('password'), 'target_reps' => 10]
        );

        $this->cleanup($user->id);

        // ─── Locations ───────────────────────────────────────────────────────
        $bc = Location::create(['user_id' => $user->id, 'name' => 'Bad Cannstatt']);
        Location::create(['user_id' => $user->id, 'name' => 'Stuttgart Mitte']);

        // ─── Trainingspläne ───────────────────────────────────────────────────
        $push = TrainingPlan::create(['location_id' => $bc->id, 'name' => 'Push']);
        $pull = TrainingPlan::create(['location_id' => $bc->id, 'name' => 'Pull']);

        // ─── Übungen ─────────────────────────────────────────────────────────
        $bench    = Exercise::create(['training_plan_id' => $push->id, 'name' => 'Bankdrücken']);
        $press    = Exercise::create(['training_plan_id' => $push->id, 'name' => 'Schulterdrücken']);
        $triPush  = Exercise::create(['training_plan_id' => $push->id, 'name' => 'Trizeps Pushdown']);
        $lateral  = Exercise::create(['training_plan_id' => $push->id, 'name' => 'Seitheben', 'is_unilateral' => true]);

        $latpull  = Exercise::create(['training_plan_id' => $pull->id, 'name' => 'Latzug weit']);
        $row      = Exercise::create(['training_plan_id' => $pull->id, 'name' => 'Rudern Kabelzug']);
        $bicep    = Exercise::create(['training_plan_id' => $pull->id, 'name' => 'Bizeps Curl']);
        $facepull = Exercise::create(['training_plan_id' => $pull->id, 'name' => 'Face Pull']);

        // ─── Wochenplan ──────────────────────────────────────────────────────
        $pushIds = [$bench->id, $press->id, $triPush->id, $lateral->id];
        $pullIds = [$latpull->id, $row->id, $bicep->id, $facepull->id];

        foreach ([
            1 => [false, $pushIds],
            2 => [false, $pullIds],
            3 => [true,  []],
            4 => [false, $pushIds],
            5 => [false, $pullIds],
            6 => [true,  []],
            7 => [true,  []],
        ] as $dow => [$rest, $exercises]) {
            $ws = WeeklySchedule::create([
                'user_id'     => $user->id,
                'day_of_week' => $dow,
                'is_rest'     => $rest,
            ]);
            if ($exercises) {
                $ws->exercises()->attach($exercises);
            }
        }

        // ─── Sessions ────────────────────────────────────────────────────────
        $log = function (Exercise $ex, Carbon $date, array $sets) {
            $session = WorkoutSession::create([
                'exercise_id' => $ex->id,
                'logged_at'   => $date->copy()->setTime(18, 0),
            ]);
            foreach ($sets as $i => $s) {
                ExerciseSet::create([
                    'workout_session_id' => $session->id,
                    'set_number'         => $i + 1,
                    'weight'             => $s[0],
                    'reps'               => $s[1] ?? null,
                    'reps_left'          => $s[2] ?? null,
                    'reps_right'         => $s[3] ?? null,
                ]);
            }
        };

        $monday = Carbon::today()->startOfWeek(Carbon::MONDAY);

        for ($week = 8; $week >= 0; $week--) {
            $progress = $week === 0 ? 1.0 : round(1 - $week / 9, 2);
            $mon = $monday->copy()->subWeeks($week);
            $tue = $mon->copy()->addDay();
            $thu = $mon->copy()->addDays(3);
            $fri = $mon->copy()->addDays(4);

            $bW  = $this->w(70,   82.5, $progress);
            $pW  = $this->w(28,   36,   $progress);
            $tW  = $this->w(25,   35,   $progress);
            $sW  = $this->w(8,    14,   $progress);
            $lW  = $this->w(50,   67.5, $progress);
            $rW  = $this->w(40,   57.5, $progress);
            $bic = $this->w(10,   16,   $progress);
            $fpW = $this->w(15,   27.5, $progress);

            if ($mon->lte(Carbon::today())) {
                $log($bench,    $mon, [[$bW, 8], [$bW, 8], [$bW + 2.5, 6], [$bW + 2.5, 6]]);
                $log($press,    $mon, [[$pW, 10], [$pW, 8], [$pW, 8]]);
            }
            if ($tue->lte(Carbon::today())) {
                $log($latpull,  $tue, [[$lW, 10], [$lW, 8], [$lW + 5, 6]]);
                $log($bicep,    $tue, [[$bic, 12], [$bic, 10], [$bic, 10], [$bic + 2, 8]]);
            }
            if ($thu->lte(Carbon::today())) {
                $log($triPush,  $thu, [[$tW, 12], [$tW, 12], [$tW + 2.5, 10]]);
                $log($lateral,  $thu, [[$sW, null, 12, 12], [$sW, null, 10, 10], [$sW + 2, null, 10, 10]]);
            }
            if ($fri->lte(Carbon::today())) {
                $log($row,      $fri, [[$rW, 12], [$rW, 10], [$rW + 5, 8]]);
                $log($facepull, $fri, [[$fpW, 15], [$fpW, 12], [$fpW, 12]]);
            }
        }

        // Bonus Mi → 4-Tage-Streak Di–Mi–Do–Fr
        $wed = $monday->copy()->addDays(2);
        if ($wed->lte(Carbon::today())) {
            $log($lateral,  $wed, [[10, null, 12, 12], [10, null, 11, 12], [12, null, 10, 10]]);
            $log($facepull, $wed, [[25, 15], [25, 12], [27.5, 12]]);
        }

        $this->command->info('Demo: demo / password');
    }

    private function w(float $min, float $max, float $progress): float
    {
        return round(($min + ($max - $min) * $progress) / 2.5) * 2.5;
    }

    private function cleanup(int $userId): void
    {
        $scheduleIds = WeeklySchedule::where('user_id', $userId)->pluck('id');
        DB::table('weekly_schedule_exercises')->whereIn('weekly_schedule_id', $scheduleIds)->delete();
        WeeklySchedule::where('user_id', $userId)->delete();

        $locIds     = Location::where('user_id', $userId)->pluck('id');
        $planIds    = TrainingPlan::whereIn('location_id', $locIds)->pluck('id');
        $exIds      = Exercise::whereIn('training_plan_id', $planIds)->pluck('id');
        $sessionIds = DB::table('workout_sessions')->whereIn('exercise_id', $exIds)->pluck('id');

        DB::table('exercise_sets')->whereIn('workout_session_id', $sessionIds)->delete();
        DB::table('workout_sessions')->whereIn('id', $sessionIds)->delete();
        DB::table('exercises')->whereIn('id', $exIds)->delete();
        DB::table('training_plans')->whereIn('id', $planIds)->delete();
        DB::table('locations')->whereIn('id', $locIds)->delete();
    }
}
