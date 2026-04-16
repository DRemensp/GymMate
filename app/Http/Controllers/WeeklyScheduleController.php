<?php

namespace App\Http\Controllers;

use App\Models\WeeklySchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WeeklyScheduleController extends Controller
{
    public function index()
    {
        $days = collect(range(1, 7))->mapWithKeys(function ($day) {
            $entry = WeeklySchedule::firstOrNew(
                ['user_id' => Auth::id(), 'day_of_week' => $day]
            );
            return [$day => $entry];
        });

        return view('weekly-schedule', compact('days'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'days'          => ['required', 'array'],
            'days.*.label'  => ['nullable', 'string', 'max:100'],
            'days.*.is_rest' => ['nullable'],
        ]);

        foreach ($data['days'] as $dayOfWeek => $values) {
            WeeklySchedule::updateOrCreate(
                ['user_id' => Auth::id(), 'day_of_week' => $dayOfWeek],
                [
                    'label'   => $values['label'] ?? null,
                    'is_rest' => isset($values['is_rest']),
                ]
            );
        }

        return back()->with('saved', true);
    }
}
