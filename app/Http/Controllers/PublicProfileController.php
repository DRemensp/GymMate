<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\User;
use App\Models\WeeklySchedule;
use App\Models\WorkoutSession;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class PublicProfileController extends Controller
{
    public function show(string $name): View
    {
        $user = User::where('name', $name)->firstOrFail();

        $sessions = WorkoutSession::whereHas('exercise.trainingPlan.location', fn($q) =>
                $q->where('user_id', $user->id))
            ->with(['sets', 'exercise'])
            ->orderByDesc('logged_at')
            ->get();

        $totalSessions = $sessions->count();
        $lastTraining  = $sessions->first()?->logged_at;

        // Total volume
        $totalVolume = $sessions->sum(fn($s) => $s->sets->sum(fn($set) => self::volume($set)));

        // Weekly streak (consecutive weeks with at least one session, counting back from current week)
        $streak = $this->calculateStreak($sessions);

        // Favorite location
        $favoriteLocation = Location::where('user_id', $user->id)
            ->get()
            ->map(fn($loc) => [
                'name'  => $loc->name,
                'count' => WorkoutSession::whereHas('exercise.trainingPlan.location', fn($q) =>
                    $q->where('id', $loc->id))->count(),
            ])
            ->sortByDesc('count')
            ->first();

        // Activity heatmap: current month dots (same style as Standorte)
        $daysInMonth = Carbon::now()->daysInMonth;
        $trainedDays = $sessions
            ->filter(fn($s) => $s->logged_at->month === now()->month && $s->logged_at->year === now()->year)
            ->map(fn($s) => $s->logged_at->day)
            ->unique()
            ->flip(); // day => true for O(1) lookup

        // Weekly schedule (Wochenplan)
        $weeklySchedule = WeeklySchedule::where('user_id', $user->id)
            ->with('exercises')
            ->get()
            ->keyBy('day_of_week');

        return view('profile.public', [
            'user'             => $user,
            'totalSessions'    => $totalSessions,
            'lastTraining'     => $lastTraining,
            'totalVolume'      => $totalVolume,
            'streak'           => $streak,
            'favoriteLocation' => $favoriteLocation,
            'trainedDays'      => $trainedDays,
            'daysInMonth'      => $daysInMonth,
            'weeklySchedule'   => $weeklySchedule,
        ]);
    }

    private function calculateStreak($sessions): int
    {
        if ($sessions->isEmpty()) {
            return 0;
        }

        $weeksWithSession = $sessions
            ->map(fn($s) => $s->logged_at->copy()->startOfWeek()->toDateString())
            ->unique()
            ->sort()
            ->values();

        $streak      = 0;
        $currentWeek = Carbon::now()->startOfWeek();

        // Allow current week to count even if not finished
        for ($i = 0; $i < $weeksWithSession->count(); $i++) {
            $week = Carbon::parse($weeksWithSession->reverse()->values()->get($i));
            $expected = $currentWeek->copy()->subWeeks($i);

            if ($week->toDateString() !== $expected->toDateString()) {
                break;
            }
            $streak++;
        }

        return $streak;
    }

    private static function volume($set): float
    {
        $reps = $set->reps !== null
            ? (int) $set->reps
            : ((int) ($set->reps_left ?? 0) + (int) ($set->reps_right ?? 0));
        return (float) $set->weight * $reps;
    }
}
