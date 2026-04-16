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
            'sets'             => ['required', 'array', 'min:1'],
            'sets.*.weight'    => ['required', 'numeric', 'min:0', 'max:9999'],
            'sets.*.reps'      => ['required', 'integer', 'min:1', 'max:9999'],
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
        $this->dispatch('session-saved');
    }

    public function render()
    {
        return view('livewire.log-workout');
    }
}
