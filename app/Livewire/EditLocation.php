<?php

namespace App\Livewire;

use App\Models\Location;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class EditLocation extends Component
{
    use WithFileUploads;

    public bool $open = false;
    public ?int $locationId = null;
    public string $name = '';
    public $image;

    #[On('edit-location')]
    public function load(int $id): void
    {
        $location = Location::findOrFail($id);
        abort_if($location->user_id !== Auth::id(), 403);

        $this->locationId = $id;
        $this->name       = $location->name;
        $this->image      = null;
        $this->open       = true;
    }

    public function save(): void
    {
        $location = Location::findOrFail($this->locationId);
        abort_if($location->user_id !== Auth::id(), 403);

        $this->validate([
            'name'  => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:4096'],
        ]);

        $location->update(['name' => $this->name]);

        if ($this->image) {
            $location->addMedia($this->image->getRealPath())
                ->usingFileName($this->image->getClientOriginalName())
                ->toMediaCollection('image');
        }

        $this->open = false;
        $this->dispatch('location-updated');
    }

    public function render()
    {
        return view('livewire.edit-location');
    }
}
