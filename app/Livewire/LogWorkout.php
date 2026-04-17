<?php

namespace App\Livewire;

use App\Models\Exercise;
use App\Models\WorkoutSession;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class LogWorkout extends Component
{
    public Exercise $exercise;

    public array $sets = [
        ['weight' => '', 'reps' => ''],
    ];

    public ?array $lastSets      = null;
    public ?string $progressionTip = null;

    public function mount(Exercise $exercise): void
    {
        $this->exercise = $exercise;

        $lastSession = $exercise->workoutSessions()->with('sets')->first();

        if ($lastSession && $lastSession->sets->isNotEmpty()) {
            $targetReps = Auth::user()->target_reps ?? 8;

            $this->lastSets = $lastSession->sets
                ->map(fn($s) => ['weight' => $s->weight, 'reps' => $s->reps])
                ->toArray();

            $allReached = $lastSession->sets->every(fn($s) => $s->reps >= $targetReps);
            $this->progressionTip = $allReached ? 'increase' : 'hold';

            $this->sets = $lastSession->sets->map(function ($set) use ($allReached) {
                $recommended = $allReached
                    ? round($set->weight * 1.05 * 2) / 2
                    : $set->weight;

                return ['weight' => $recommended, 'reps' => $set->reps];
            })->toArray();
        }
    }

    public function addSet(): void
    {
        $this->sets[] = ['weight' => '', 'reps' => ''];
    }

    public function removeSet(int $index): void
    {
        if (count($this->sets) > 1) {
            array_splice($this->sets, $index, 1);
        }
    }

    public function save(): void
    {
        abort_if($this->exercise->trainingPlan->location->user_id !== Auth::id(), 403);

        $this->validate([
            'sets'          => ['required', 'array', 'min:1'],
            'sets.*.weight' => ['required', 'numeric', 'min:0', 'max:9999'],
            'sets.*.reps'   => ['required', 'integer', 'min:1', 'max:9999'],
        ]);

        $session = WorkoutSession::create([
            'exercise_id' => $this->exercise->id,
            'logged_at'   => now(),
        ]);

        foreach ($this->sets as $i => $set) {
            $session->sets()->create([
                'set_number' => $i + 1,
                'weight'     => $set['weight'],
                'reps'       => $set['reps'],
            ]);
        }

        $this->sets = [['weight' => '', 'reps' => '']];
        $this->lastSets = null;
        $this->progressionTip = null;
        $this->dispatch('session-saved');
    }

    public function render()
    {
        return view('livewire.log-workout');
    }
}
