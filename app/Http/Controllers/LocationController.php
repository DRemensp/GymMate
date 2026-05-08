<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\WeeklySchedule;
use App\Models\WorkoutSession;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LocationController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $user   = Auth::user();

        $hour     = now()->hour;
        $greeting = match(true) {
            $hour < 12 => 'Guten Morgen',
            $hour < 18 => 'Guten Tag',
            default    => 'Guten Abend',
        };
        $firstName = explode(' ', $user->name)[0];

        $todayDow     = Carbon::today()->isoWeekday();
        $dayNames     = [1=>'Montag',2=>'Dienstag',3=>'Mittwoch',4=>'Donnerstag',5=>'Freitag',6=>'Samstag',7=>'Sonntag'];
        $todayDayName = $dayNames[$todayDow];

        $todaySchedule = WeeklySchedule::where('user_id', $userId)
            ->where('day_of_week', $todayDow)
            ->with('exercises')
            ->first();

        $locations = $user->locations()
            ->with(['media', 'trainingPlans.exercises'])
            ->orderBy('name')
            ->get()
            ->map(function ($location) use ($userId) {
                $exerciseIds = $location->trainingPlans->flatMap->exercises->pluck('id');

                if ($exerciseIds->isEmpty()) {
                    $location->sessionCount = 0;
                    $location->lastVisit    = null;
                    $location->monthDots    = array_fill(0, Carbon::now()->daysInMonth, false);
                    return $location;
                }

                $location->sessionCount = WorkoutSession::whereIn('exercise_id', $exerciseIds)->count();

                $lastSession = WorkoutSession::whereIn('exercise_id', $exerciseIds)
                    ->latest('logged_at')
                    ->first();
                $location->lastVisit = $lastSession?->logged_at;

                $sessionsThisMonth = WorkoutSession::whereIn('exercise_id', $exerciseIds)
                    ->whereBetween('logged_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfDay()])
                    ->get(['logged_at']);

                $trainedDays = $sessionsThisMonth
                    ->map(fn($s) => Carbon::parse($s->logged_at)->day)
                    ->flip()
                    ->toArray();

                $location->monthDots = array_map(fn($d) => isset($trainedDays[$d]), range(1, Carbon::now()->daysInMonth));

                return $location;
            });

        return view('dashboard', compact(
            'locations', 'greeting', 'firstName',
            'todaySchedule', 'todayDayName'
        ));
    }

    public function destroy(Location $location)
    {
        abort_if($location->user_id !== Auth::id(), 403);

        $location->delete();

        return redirect()->route('dashboard');
    }

}
