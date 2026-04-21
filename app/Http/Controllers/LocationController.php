<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\WeeklySchedule;
use App\Models\WorkoutSession;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Auth::user()->locations()->with('media')->orderBy('name')->get();
        $userId    = Auth::id();

        $todayDow = Carbon::today()->isoWeekday();

        // Alle 7 Wochentage laden
        $schedule = WeeklySchedule::where('user_id', $userId)
            ->get()
            ->keyBy('day_of_week');

        // Heute geloggt?
        $loggedToday = WorkoutSession::whereHas('exercise.trainingPlan.location', fn($q) => $q->where('user_id', $userId))
            ->whereDate('logged_at', Carbon::today())
            ->exists();

        return view('dashboard', compact('locations', 'schedule', 'todayDow', 'loggedToday'));
    }

    public function destroy(Location $location)
    {
        abort_if($location->user_id !== Auth::id(), 403);

        $location->delete();

        return redirect()->route('dashboard');
    }
}
