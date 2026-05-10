<?php

namespace App\Livewire;

use App\Models\Location;
use App\Models\TrainingPlan;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreateLocation extends Component
{
    use WithFileUploads;

    public bool $open = false;
    public string $name = '';
    public $image;

    public function save(): void
    {
        $this->validate([
            'name'  => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:4096'],
        ]);

        $location = Auth::user()->locations()->create(['name' => $this->name]);

        foreach (['Schulter', 'Arme', 'Rücken', 'Brust', 'Beine'] as $plan) {
            TrainingPlan::create(['location_id' => $location->id, 'name' => $plan]);
        }

        if ($this->image) {
            $location->addMedia($this->image->getRealPath())
                ->usingFileName($this->image->getClientOriginalName())
                ->toMediaCollection('image');
        }

        $this->reset('name', 'image', 'open');
        $this->dispatch('location-created');
    }

    public function render()
    {
        return view('livewire.create-location');
    }
}
