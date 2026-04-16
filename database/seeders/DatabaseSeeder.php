<?php

namespace Database\Seeders;

use App\Models\Exercise;
use App\Models\Location;
use App\Models\TrainingPlan;
use App\Models\User;
use App\Models\WorkoutSession;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $user = User::factory()->create([
            'name'     => 'admin',
            'email'    => 'admin@gymmate.local',
            'password' => bcrypt('admin123'),
        ]);

        $location = Location::create([
            'user_id' => $user->id,
            'name'    => 'FitnessFabrik',
        ]);

        $plan = TrainingPlan::create([
            'location_id' => $location->id,
            'name'        => 'Push / Pull / Legs',
        ]);

        $exercises = [
            ['name' => 'Bankdrücken',       'description' => 'Flachbank, Griffbreite schulterbreit'],
            ['name' => 'Schrägbankdrücken', 'description' => 'Obere Brust, 30° Neigung'],
            ['name' => 'Kabelzug Trizeps',  'description' => 'Am Seilzug, Ellbogen fixiert'],
            ['name' => 'Klimmzüge',         'description' => 'Schulterbreit, volle Bewegung'],
            ['name' => 'Rudern Maschine',   'description' => 'Brust angelegt, Schulterblätter zusammen'],
        ];

        $dates = [
            // ~4 Monate zurück, einmal pro Woche
            Carbon::now()->subMonths(4)->startOfWeek(),
            Carbon::now()->subMonths(4)->startOfWeek()->addDays(3),
            Carbon::now()->subMonths(3)->startOfWeek(),
            Carbon::now()->subMonths(3)->startOfWeek()->addDays(4),
            Carbon::now()->subMonths(3)->startOfWeek()->addWeeks(2),
            Carbon::now()->subMonths(2)->startOfWeek(),
            Carbon::now()->subMonths(2)->startOfWeek()->addWeeks(1),
            Carbon::now()->subMonths(2)->startOfWeek()->addWeeks(2),
            Carbon::now()->subMonths(1)->startOfWeek(),
            Carbon::now()->subMonths(1)->startOfWeek()->addWeeks(1),
            Carbon::now()->subWeeks(3),
            Carbon::now()->subWeeks(2),
            Carbon::now()->subWeeks(1),
            Carbon::now()->subDays(3),
        ];

        foreach ($exercises as $exerciseData) {
            $exercise = Exercise::create([
                'training_plan_id' => $plan->id,
                'name'             => $exerciseData['name'],
                'description'      => $exerciseData['description'],
            ]);

            // Basis-Gewicht pro Übung
            $baseWeight = match($exercise->name) {
                'Bankdrücken'       => 80,
                'Schrägbankdrücken' => 70,
                'Kabelzug Trizeps'  => 35,
                'Klimmzüge'         => 90,
                'Rudern Maschine'   => 65,
                default             => 60,
            };

            foreach ($dates as $i => $date) {
                // Leichte Progression über Zeit simulieren
                $progression = round($i * 1.25, 1);

                $session = WorkoutSession::create([
                    'exercise_id' => $exercise->id,
                    'logged_at'   => $date,
                ]);

                // 3–4 Sets pro Einheit
                $setCount = ($i % 3 === 0) ? 4 : 3;
                for ($s = 0; $s < $setCount; $s++) {
                    // Letzter Set meist etwas weniger Gewicht
                    $setWeight = $s < $setCount - 1
                        ? $baseWeight + $progression
                        : $baseWeight + $progression - 5;

                    $session->sets()->create([
                        'set_number' => $s + 1,
                        'weight'     => $setWeight,
                        'reps'       => match($s) {
                            0       => 12,
                            1       => 10,
                            2       => 8,
                            default => 6,
                        },
                    ]);
                }
            }
        }
    }
}
