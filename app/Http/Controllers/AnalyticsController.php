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
        $totalVolume   = $sessions->sum(fn($s) => $s->sets->sum(fn($set) => $set->weight * $set->reps));
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
                ->sum(fn($s) => $s->sets->sum(fn($set) => $set->weight * $set->reps)));
        }

        // Per-exercise stats
        $exerciseStats = $sessions
            ->groupBy('exercise_id')
            ->map(function ($exerciseSessions) {
                $exercise  = $exerciseSessions->first()->exercise;
                $allSets   = $exerciseSessions->flatMap->sets;
                $maxWeight = $allSets->max('weight') ?? 0;
                $best1RM   = round($allSets->max(fn($s) => $s->weight * (1 + $s->reps / 30)) ?? 0, 1);
                $totalVol  = $exerciseSessions->sum(fn($s) => $s->sets->sum(fn($set) => $set->weight * $set->reps));
                $sparkline = $exerciseSessions->sortBy('logged_at')->slice(-10)
                    ->map(fn($s) => round($s->sets->sum(fn($set) => $set->weight * $set->reps)))
                    ->values()->toArray();

                return [
                    'name'         => $exercise->name,
                    'sessions'     => $exerciseSessions->count(),
                    'max_weight'   => $maxWeight,
                    'best_1rm'     => $best1RM,
                    'total_volume' => round($totalVol),
                    'last_trained' => $exerciseSessions->sortByDesc('logged_at')->first()->logged_at->format('d.m.Y'),
                    'sparkline'    => $sparkline,
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
}
