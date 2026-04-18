<?php

namespace App\Livewire;

use App\Models\TrainingPlan;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class EditTrainingPlan extends Component
{
    use WithFileUploads;

    public bool $open = false;
    public ?int $planId = null;
    public string $name = '';
    public $image;

    #[On('edit-training-plan')]
    public function load(int $id): void
    {
        $plan = TrainingPlan::findOrFail($id);
        abort_if($plan->location->user_id !== Auth::id(), 403);

        $this->planId = $id;
        $this->name   = $plan->name;
        $this->image  = null;
        $this->open   = true;
    }

    public function save(): void
    {
        $plan = TrainingPlan::findOrFail($this->planId);
        abort_if($plan->location->user_id !== Auth::id(), 403);

        $this->validate([
            'name'  => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:4096'],
        ]);

        $plan->update(['name' => $this->name]);

        if ($this->image) {
            $plan->addMedia($this->image->getRealPath())
                ->usingFileName($this->image->getClientOriginalName())
                ->toMediaCollection('image');
        }

        $this->open = false;
        $this->dispatch('plan-updated');
    }

    public function render()
    {
        return view('livewire.edit-training-plan');
    }
}
