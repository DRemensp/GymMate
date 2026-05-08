<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\WorkoutSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $userId    = Auth::id();
        $locations = Location::where('user_id', $userId)->orderBy('name')->get();

        $locationId = $request->query('location', $locations->first()?->id);
        $active     = $locations->firstWhere('id', $locationId) ?? $locations->first();

        $sessions = WorkoutSession::whereHas('exercise.trainingPlan.location', fn($q) =>
                $q->where('user_id', $userId)->where('id', $active?->id))
            ->with(['sets', 'exercise'])
            ->orderBy('logged_at')
            ->get();

        // Summary stats
        $totalSessions = $sessions->count();
        $totalVolume   = $sessions->sum(fn($s) => $s->sets->sum(fn($set) => $set->weight * self::effectiveReps($set)));
        $totalSets     = $sessions->sum(fn($s) => $s->sets->count());

        // Weekly volume (last 16 weeks)
        $weeklyLabels = [];
        $weeklyData   = [];
        for ($i = 15; $i >= 0; $i--) {
            $weekStart      = Carbon::now()->startOfWeek()->subWeeks($i);
            $weekEnd        = $weekStart->copy()->endOfWeek();
            $weeklyLabels[] = $weekStart->format('d.m.');
            $weeklyData[]   = round($sessions
                ->filter(fn($s) => $s->logged_at->between($weekStart, $weekEnd))
                ->sum(fn($s) => $s->sets->sum(fn($set) => $set->weight * self::effectiveReps($set))));
        }

        // Per-exercise stats
        $exerciseStats = $sessions
            ->groupBy('exercise_id')
            ->map(function ($exerciseSessions) {
                $exercise  = $exerciseSessions->first()->exercise;
                $allSets   = $exerciseSessions->flatMap->sets;
                $maxWeight = $allSets->max('weight') ?? 0;
                $best1RM   = round($allSets->max(fn($s) => $s->weight * (1 + self::effectiveReps($s, max: true) / 30)) ?? 0, 1);
                $totalVol  = $exerciseSessions->sum(fn($s) => $s->sets->sum(fn($set) => $set->weight * self::effectiveReps($set)));
                $lastTen         = $exerciseSessions->sortBy('logged_at')->slice(-10);
                $sparkline       = $lastTen->map(fn($s) => round($s->sets->sum(fn($set) => $set->weight * self::effectiveReps($set))))->values()->toArray();
                $sparklineWeight = $lastTen->map(fn($s) => (float) $s->sets->max('weight'))->values()->toArray();

                return [
                    'name'            => $exercise->name,
                    'sessions'        => $exerciseSessions->count(),
                    'max_weight'      => $maxWeight,
                    'best_1rm'        => $best1RM,
                    'total_volume'    => round($totalVol),
                    'last_trained'    => $exerciseSessions->sortByDesc('logged_at')->first()->logged_at->format('d.m.Y'),
                    'sparkline'       => $sparkline,
                    'sparkline_weight' => $sparklineWeight,
                ];
            })
            ->sortByDesc('total_volume')
            ->values();

        return view('analytics', compact(
            'locations', 'active',
            'totalSessions', 'totalVolume', 'totalSets',
            'weeklyLabels', 'weeklyData', 'exerciseStats'
        ));
    }

    // For bilateral: reps. For unilateral: sum of both sides (volume) or max side (1RM).
    private static function effectiveReps($set, bool $max = false): int
    {
        if ($set->reps !== null) {
            return (int) $set->reps;
        }
        $left  = (int) ($set->reps_left  ?? 0);
        $right = (int) ($set->reps_right ?? 0);
        return $max ? max($left, $right) : ($left + $right);
    }
}
