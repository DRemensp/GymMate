<?php

namespace App\Livewire;

use App\Models\Exercise;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class EditExercise extends Component
{
    use WithFileUploads;

    public bool $open = false;
    public ?int $exerciseId = null;
    public string $name = '';
    public string $description = '';
    public bool $is_unilateral = false;
    public $image;

    #[On('edit-exercise')]
    public function load(int $id): void
    {
        $exercise = Exercise::findOrFail($id);
        abort_if($exercise->trainingPlan->location->user_id !== Auth::id(), 403);

        $this->exerciseId    = $id;
        $this->name          = $exercise->name;
        $this->description   = $exercise->description ?? '';
        $this->is_unilateral = $exercise->is_unilateral;
        $this->image         = null;
        $this->open          = true;
    }

    public function save(): void
    {
        $exercise = Exercise::findOrFail($this->exerciseId);
        abort_if($exercise->trainingPlan->location->user_id !== Auth::id(), 403);

        $this->validate([
            'name'          => ['required', 'string', 'max:255'],
            'description'   => ['nullable', 'string'],
            'is_unilateral' => ['boolean'],
            'image'         => ['nullable', 'image', 'max:4096'],
        ]);

        $exercise->update([
            'name'          => $this->name,
            'description'   => $this->description ?: null,
            'is_unilateral' => $this->is_unilateral,
        ]);

        if ($this->image) {
            $exercise->addMedia($this->image->getRealPath())
                ->usingFileName($this->image->getClientOriginalName())
                ->toMediaCollection('image');
        }

        $this->open = false;
        $this->dispatch('exercise-updated');
    }

    public function render()
    {
        return view('livewire.edit-exercise');
    }
}
