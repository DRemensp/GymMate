<?php

namespace App\Livewire;

use App\Models\WorkoutSession;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class EditWorkoutSession extends Component
{
    public bool $open = false;
    public bool $showUndo = false;
    public ?int $sessionId = null;
    public ?int $deletedSessionId = null;
    public string $sessionDate = '';
    public array $sets = [];

    #[On('load-session')]
    public function load(int $id): void
    {
        $session = WorkoutSession::with('sets')->findOrFail($id);
        abort_if($session->exercise->trainingPlan->location->user_id !== Auth::id(), 403);

        $this->sessionId   = $id;
        $this->sessionDate = $session->logged_at->format('d.m.Y H:i');
        $this->sets        = $session->sets->map(fn($s) => [
            'weight' => $s->weight,
            'reps'   => $s->reps,
        ])->toArray();

        $this->showUndo = false;
        $this->open = true;
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
        $this->validate([
            'sets'          => ['required', 'array', 'min:1'],
            'sets.*.weight' => ['required', 'numeric', 'min:0', 'max:9999'],
            'sets.*.reps'   => ['required', 'integer', 'min:1', 'max:9999'],
        ]);

        $session = WorkoutSession::findOrFail($this->sessionId);
        abort_if($session->exercise->trainingPlan->location->user_id !== Auth::id(), 403);

        $session->sets()->delete();

        foreach ($this->sets as $i => $set) {
            $session->sets()->create([
                'set_number' => $i + 1,
                'weight'     => $set['weight'],
                'reps'       => $set['reps'],
            ]);
        }

        $this->open = false;
        $this->dispatch('session-saved');
    }

    public function delete(): void
    {
        $session = WorkoutSession::findOrFail($this->sessionId);
        abort_if($session->exercise->trainingPlan->location->user_id !== Auth::id(), 403);

        $session->delete();

        $this->deletedSessionId = $this->sessionId;
        $this->open = false;
        $this->showUndo = true;
    }

    public function restore(): void
    {
        WorkoutSession::withTrashed()->findOrFail($this->deletedSessionId)->restore();
        $this->showUndo = false;
        $this->deletedSessionId = null;
        $this->dispatch('session-saved');
    }

    public function permanentlyDelete(): void
    {
        if ($this->deletedSessionId) {
            WorkoutSession::withTrashed()->findOrFail($this->deletedSessionId)->forceDelete();
        }
        $this->showUndo = false;
        $this->deletedSessionId = null;
        $this->dispatch('session-saved');
    }

    public function render()
    {
        return view('livewire.edit-workout-session');
    }
}
