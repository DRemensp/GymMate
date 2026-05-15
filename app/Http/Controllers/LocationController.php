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
            ->get();

        // Build exercise_id → location_id map from already-loaded relations (no extra queries)
        $exerciseToLocation = [];
        foreach ($locations as $location) {
            foreach ($location->trainingPlans as $plan) {
                foreach ($plan->exercises as $exercise) {
                    $exerciseToLocation[$exercise->id] = $location->id;
                }
            }
        }

        $daysInMonth = Carbon::now()->daysInMonth;
        $monthStart  = Carbon::now()->startOfMonth();
        $monthEnd    = Carbon::now()->endOfDay();

        foreach ($locations as $location) {
            $location->sessionCount = 0;
            $location->lastVisit    = null;
            $location->monthDots    = array_fill(0, $daysInMonth, false);
        }

        if (!empty($exerciseToLocation)) {
            // Single query replaces 3N queries
            $allSessions = WorkoutSession::whereIn('exercise_id', array_keys($exerciseToLocation))
                ->get(['exercise_id', 'logged_at']);

            $countByLocation       = [];
            $lastVisitByLocation   = [];
            $trainedDaysByLocation = [];

            foreach ($allSessions as $session) {
                $locId    = $exerciseToLocation[$session->exercise_id];
                $loggedAt = Carbon::parse($session->logged_at);

                $countByLocation[$locId] = ($countByLocation[$locId] ?? 0) + 1;

                if (!isset($lastVisitByLocation[$locId]) || $loggedAt > $lastVisitByLocation[$locId]) {
                    $lastVisitByLocation[$locId] = $loggedAt;
                }

                if ($loggedAt->between($monthStart, $monthEnd)) {
                    $trainedDaysByLocation[$locId][$loggedAt->day] = true;
                }
            }

            $locationMap = $locations->keyBy('id');
            foreach ($countByLocation as $locId => $count) {
                $loc = $locationMap[$locId] ?? null;
                if (!$loc) continue;
                $loc->sessionCount = $count;
                $loc->lastVisit    = $lastVisitByLocation[$locId] ?? null;
                $days = $trainedDaysByLocation[$locId] ?? [];
                $loc->monthDots = array_map(fn($d) => isset($days[$d]), range(1, $daysInMonth));
            }
        }

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
