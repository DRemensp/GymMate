<?php

namespace App\Livewire;

use App\Models\Location;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreateTrainingPlan extends Component
{
    use WithFileUploads;

    public Location $location;
    public bool $open = false;
    public string $name = '';
    public $image;

    public function save(): void
    {
        $this->validate([
            'name'  => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:4096'],
        ]);

        $plan = $this->location->trainingPlans()->create(['name' => $this->name]);

        if ($this->image) {
            $plan->addMedia($this->image->getRealPath())
                ->usingFileName($this->image->getClientOriginalName())
                ->toMediaCollection('image');
        }

        $this->reset('name', 'image', 'open');
        $this->dispatch('plan-created');
    }

    public function render()
    {
        return view('livewire.create-training-plan');
    }
}
