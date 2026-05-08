<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use App\Models\Location;
use App\Models\WeeklySchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WeeklyScheduleController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Ensure all 7 days exist so we can sync exercises to them
        foreach (range(1, 7) as $day) {
            WeeklySchedule::firstOrCreate(['user_id' => $userId, 'day_of_week' => $day]);
        }

        $days = WeeklySchedule::where('user_id', $userId)
            ->with('exercises')
            ->get()
            ->keyBy('day_of_week');

        $exerciseGroups = Location::where('user_id', $userId)
            ->with(['trainingPlans' => fn($q) => $q->orderBy('name'),
                    'trainingPlans.exercises' => fn($q) => $q->orderBy('name')])
            ->orderBy('name')
            ->get();

        return view('weekly-schedule', compact('days', 'exerciseGroups'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'days'               => ['required', 'array'],
            'days.*.is_rest'     => ['nullable'],
            'days.*.exercises'   => ['nullable', 'array'],
            'days.*.exercises.*' => ['integer', 'exists:exercises,id'],
            'target_reps'        => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $userId = Auth::id();

        $userExerciseIds = Exercise::whereHas('trainingPlan.location', fn($q) => $q->where('user_id', $userId))
            ->pluck('id');

        foreach ($data['days'] as $dayOfWeek => $values) {
            $schedule = WeeklySchedule::updateOrCreate(
                ['user_id' => $userId, 'day_of_week' => $dayOfWeek],
                ['is_rest' => isset($values['is_rest'])]
            );

            $exerciseIds = collect($values['exercises'] ?? [])
                ->filter(fn($id) => $userExerciseIds->contains((int) $id))
                ->values();

            $schedule->exercises()->sync($exerciseIds);
        }

        Auth::user()->update(['target_reps' => $data['target_reps'] ?? 8]);

        return back()->with('saved', true);
    }
}
