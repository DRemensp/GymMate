<?php

namespace App\Livewire;

use App\Models\Exercise;
use App\Models\ExerciseSet;
use App\Models\WorkoutSession;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class LogWorkout extends Component
{
    public Exercise $exercise;

    public array $sets = [
        ['weight' => '', 'reps' => '', 'reps_left' => '', 'reps_right' => ''],
    ];

    public ?array $lastSets        = null;
    public ?string $progressionTip = null;
    public ?float $recommendedWeight = null;
    public string $loggedAt          = '';

    public function mount(Exercise $exercise): void
    {
        $this->exercise  = $exercise;
        $this->loggedAt  = now()->format('Y-m-d');

        $lastSession = $exercise->workoutSessions()->with('sets')->first();

        if ($lastSession && $lastSession->sets->isNotEmpty()) {
            $targetReps = Auth::user()->target_reps ?? 8;

            $this->lastSets = $lastSession->sets->map(fn($s) => [
                'weight'     => $s->weight,
                'reps'       => $s->reps,
                'reps_left'  => $s->reps_left,
                'reps_right' => $s->reps_right,
            ])->toArray();

            if ($exercise->is_unilateral) {
                $allReached = $lastSession->sets->every(
                    fn($s) => $s->reps_left >= $targetReps && $s->reps_right >= $targetReps
                );
            } else {
                $allReached = $lastSession->sets->every(fn($s) => $s->reps >= $targetReps);
            }

            $this->progressionTip = $allReached ? 'increase' : 'hold';
            $this->sets = array_fill(0, $lastSession->sets->count(), ['weight' => '', 'reps' => '', 'reps_left' => '', 'reps_right' => '']);

            // All unique weights ever used for this exercise, sorted ascending
            $usedWeights = ExerciseSet::whereHas('session', fn($q) => $q->where('exercise_id', $exercise->id))
                ->pluck('weight')
                ->filter(fn($w) => $w > 0)
                ->map(fn($w) => (float) $w)
                ->unique()
                ->sort()
                ->values();

            $lastMax = (float) $lastSession->sets->max('weight');

            if ($this->progressionTip === 'increase') {
                // Next heavier weight actually used on this machine — no math, no made-up values
                $next = $usedWeights->first(fn($w) => $w > $lastMax);
                $this->recommendedWeight = $next; // null = no heavier weight in history yet
            } else {
                $this->recommendedWeight = $lastMax;
            }
        }
    }

    public function addSet(): void
    {
        $this->sets[] = ['weight' => '', 'reps' => '', 'reps_left' => '', 'reps_right' => ''];
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

        $baseRules = [
            'loggedAt' => ['required', 'date', 'before_or_equal:today'],
        ];

        if ($this->exercise->is_unilateral) {
            $this->validate($baseRules + [
                'sets'              => ['required', 'array', 'min:1'],
                'sets.*.weight'     => ['required', 'numeric', 'min:0', 'max:9999'],
                'sets.*.reps_left'  => ['required', 'integer', 'min:1', 'max:9999'],
                'sets.*.reps_right' => ['required', 'integer', 'min:1', 'max:9999'],
            ]);
        } else {
            $this->validate($baseRules + [
                'sets'          => ['required', 'array', 'min:1'],
                'sets.*.weight' => ['required', 'numeric', 'min:0', 'max:9999'],
                'sets.*.reps'   => ['required', 'integer', 'min:1', 'max:9999'],
            ]);
        }

        $session = WorkoutSession::create([
            'exercise_id' => $this->exercise->id,
            'logged_at'   => $this->loggedAt,
        ]);

        foreach ($this->sets as $i => $set) {
            $session->sets()->create([
                'set_number' => $i + 1,
                'weight'     => $set['weight'],
                'reps'       => $this->exercise->is_unilateral ? null : $set['reps'],
                'reps_left'  => $this->exercise->is_unilateral ? $set['reps_left'] : null,
                'reps_right' => $this->exercise->is_unilateral ? $set['reps_right'] : null,
            ]);
        }

        $this->sets = [['weight' => '', 'reps' => '', 'reps_left' => '', 'reps_right' => '']];
        $this->lastSets = null;
        $this->progressionTip = null;
        $this->loggedAt = now()->format('Y-m-d');
        $this->dispatch('session-saved');
    }

    public function render()
    {
        return view('livewire.log-workout');
    }
}
