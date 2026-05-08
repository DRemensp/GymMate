<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\TrainingPlan;
use App\Models\WeeklySchedule;
use App\Models\WorkoutSession;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class TrainingPlanController extends Controller
{
    public function index(Location $location)
    {
        abort_if($location->user_id !== Auth::id(), 403);

        $userId = Auth::id();
        $plans  = $location->trainingPlans()->with('media')->orderBy('name')->get();

        $todayDow = Carbon::today()->isoWeekday();

        $schedule = WeeklySchedule::where('user_id', $userId)
            ->with('exercises')
            ->get()
            ->keyBy('day_of_week');

        // Map each day-of-week to its actual upcoming calendar date (today = offset 0)
        $weekDates = collect(range(0, 6))->mapWithKeys(function ($i) use ($todayDow) {
            $dow  = (($todayDow - 1 + $i) % 7) + 1;
            $date = Carbon::today()->addDays($i)->toDateString();
            return [$dow => $date];
        });

        // Build a "exerciseId|YYYY-MM-DD" lookup set for sessions logged this week window
        $loggedSet     = [];
        $scheduledIds  = $schedule->flatMap->exercises->pluck('id')->unique();

        if ($scheduledIds->isNotEmpty()) {
            WorkoutSession::whereIn('exercise_id', $scheduledIds)
                ->whereBetween('logged_at', [
                    Carbon::today()->startOfDay(),
                    Carbon::today()->addDays(6)->endOfDay(),
                ])
                ->get(['exercise_id', 'logged_at'])
                ->each(function ($s) use (&$loggedSet) {
                    $loggedSet[$s->exercise_id . '|' . Carbon::parse($s->logged_at)->toDateString()] = true;
                });
        }

        return view('training-plans.index', compact(
            'location', 'plans', 'schedule', 'todayDow', 'weekDates', 'loggedSet'
        ));
    }

    public function destroy(TrainingPlan $trainingPlan)
    {
        abort_if($trainingPlan->location->user_id !== Auth::id(), 403);

        $trainingPlan->delete();

        return redirect()->route('locations.training-plans.index', $trainingPlan->location_id);
    }
}
