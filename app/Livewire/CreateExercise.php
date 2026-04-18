<?php

namespace App\Livewire;

use App\Models\TrainingPlan;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreateExercise extends Component
{
    use WithFileUploads;

    public TrainingPlan $trainingPlan;
    public bool $open = false;
    public string $name = '';
    public string $description = '';
    public bool $is_unilateral = false;
    public $image;

    public function save(): void
    {
        $this->validate([
            'name'         => ['required', 'string', 'max:255'],
            'description'  => ['nullable', 'string'],
            'is_unilateral'=> ['boolean'],
            'image'        => ['nullable', 'image', 'max:4096'],
        ]);

        $exercise = $this->trainingPlan->exercises()->create([
            'name'          => $this->name,
            'description'   => $this->description ?: null,
            'is_unilateral' => $this->is_unilateral,
        ]);

        if ($this->image) {
            $exercise->addMedia($this->image->getRealPath())
                ->usingFileName($this->image->getClientOriginalName())
                ->toMediaCollection('image');
        }

        $this->reset('name', 'description', 'is_unilateral', 'image', 'open');
        $this->dispatch('exercise-created');
    }

    public function render()
    {
        return view('livewire.create-exercise');
    }
}
