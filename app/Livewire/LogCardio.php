<?php

namespace App\Livewire;

use App\Models\CardioSession;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class LogCardio extends Component
{
    public string $activity        = 'laufband';
    public string $duration        = '';
    public string $distance        = '';
    public string $intensity       = 'mittel';
    public string $hiitRounds      = '';
    public string $hiitWork        = '40';
    public string $hiitRest        = '20';
    public string $notes           = '';
    public string $loggedAt        = '';

    public function mount(): void
    {
        $this->loggedAt = now()->format('Y-m-d');
    }

    public function isHiit(): bool
    {
        return $this->activity === 'hiit';
    }

    public function hasDistance(): bool
    {
        return in_array($this->activity, ['laufband', 'fahrrad', 'rudergeraet']);
    }

    public function save(): void
    {
        $rules = [
            'activity'  => ['required', 'string'],
            'intensity' => ['required', 'in:leicht,mittel,intensiv'],
            'loggedAt'  => ['required', 'date', 'before_or_equal:today'],
        ];

        if ($this->isHiit()) {
            $rules['hiitRounds'] = ['required', 'integer', 'min:1', 'max:99'];
            $rules['hiitWork']   = ['required', 'integer', 'min:5', 'max:600'];
            $rules['hiitRest']   = ['required', 'integer', 'min:5', 'max:600'];
            $rules['duration']   = ['nullable', 'integer', 'min:1', 'max:999'];
        } else {
            $rules['duration']   = ['required', 'integer', 'min:1', 'max:999'];
            if ($this->hasDistance()) {
                $rules['distance'] = ['nullable', 'numeric', 'min:0', 'max:999'];
            }
        }

        $this->validate($rules);

        $durationMinutes = $this->isHiit() && !$this->duration
            ? (int) round(($this->hiitRounds * ($this->hiitWork + $this->hiitRest)) / 60)
            : (int) $this->duration;

        CardioSession::create([
            'user_id'           => Auth::id(),
            'activity'          => $this->activity,
            'duration_minutes'  => max(1, $durationMinutes),
            'distance_km'       => $this->hasDistance() && $this->distance ? $this->distance : null,
            'intensity'         => $this->intensity,
            'hiit_rounds'       => $this->isHiit() ? $this->hiitRounds : null,
            'hiit_work_seconds' => $this->isHiit() ? $this->hiitWork : null,
            'hiit_rest_seconds' => $this->isHiit() ? $this->hiitRest : null,
            'notes'             => $this->notes ?: null,
            'logged_at'         => $this->loggedAt,
        ]);

        $this->reset('duration', 'distance', 'hiitRounds', 'notes');
        $this->hiitWork = '40';
        $this->hiitRest = '20';
        $this->loggedAt = now()->format('Y-m-d');
        $this->dispatch('cardio-saved');
    }

    public function render()
    {
        return view('livewire.log-cardio', [
            'activities' => CardioSession::ACTIVITIES,
        ]);
    }
}
