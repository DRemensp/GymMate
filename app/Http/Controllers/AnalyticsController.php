<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\User;
use App\Models\WorkoutSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class AnalyticsController extends Controller
{
    // Time buckets: name => [hours in bucket, display range, representative hour]
    private const BUCKETS = [
        'Morgen'    => ['hours' => [5,6,7,8,9],       'range' => '05–09 Uhr', 'repHour' => 7],
        'Vormittag' => ['hours' => [10,11,12,13],      'range' => '10–13 Uhr', 'repHour' => 11],
        'Nachmittag'=> ['hours' => [14,15,16,17],      'range' => '14–17 Uhr', 'repHour' => 16],
        'Abend'     => ['hours' => [18,19,20,21],      'range' => '18–21 Uhr', 'repHour' => 19],
        'Nacht'     => ['hours' => [22,23,0,1,2,3,4], 'range' => '22–04 Uhr', 'repHour' => 23],
    ];

    public function index(Request $request)
    {
        $userId    = Auth::id();
        $user      = Auth::user();
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
                    'name'             => $exercise->name,
                    'sessions'         => $exerciseSessions->count(),
                    'max_weight'       => $maxWeight,
                    'best_1rm'         => $best1RM,
                    'total_volume'     => round($totalVol),
                    'last_trained'     => $exerciseSessions->sortByDesc('logged_at')->first()->logged_at->format('d.m.Y'),
                    'sparkline'        => $sparkline,
                    'sparkline_weight' => $sparklineWeight,
                ];
            })
            ->sortByDesc('total_volume')
            ->values();

        // PrimeTime analysis (uses all locations for more data)
        $primeTime = $this->calculatePrimeTime($userId, $user);

        return view('analytics', compact(
            'locations', 'active',
            'totalSessions', 'totalVolume', 'totalSets',
            'weeklyLabels', 'weeklyData', 'exerciseStats',
            'primeTime'
        ));
    }

    private function calculatePrimeTime(int $userId, User $user): ?array
    {
        // Build hour → bucket lookup
        $hourToBucket = [];
        foreach (self::BUCKETS as $name => $def) {
            foreach ($def['hours'] as $h) {
                $hourToBucket[$h] = $name;
            }
        }

        // Load all sessions across all locations (just need exercise_id, created_at, sets)
        $allSessions = WorkoutSession::whereHas('exercise.trainingPlan.location',
                fn($q) => $q->where('user_id', $userId))
            ->with('sets')
            ->select(['id', 'exercise_id', 'created_at'])
            ->get();

        if ($allSessions->count() < 5) {
            return null;
        }

        $bucketDeltas = [];

        foreach ($allSessions->groupBy('exercise_id') as $exerciseSessions) {
            if ($exerciseSessions->count() < 4) {
                continue;
            }

            $sorted = $exerciseSessions->sortBy('created_at')->values();

            // Skip exercises where all sessions are in the same time bucket (nothing to compare)
            $uniqueBuckets = $sorted
                ->map(fn($s) => $hourToBucket[$s->created_at->hour] ?? 'Nacht')
                ->unique();
            if ($uniqueBuckets->count() < 2) {
                continue;
            }

            $firstTs = $sorted->first()->created_at;

            $points = $sorted->map(function ($s) use ($firstTs, $hourToBucket) {
                // x = days since first session of this exercise
                $x = (float) $firstTs->diffInDays($s->created_at);
                // y = estimated 1RM of the best set this session
                $y = $s->sets->max(function ($set) {
                    $reps = $set->reps !== null
                        ? (int) $set->reps
                        : max((int)($set->reps_left ?? 0), (int)($set->reps_right ?? 0));
                    return (float) $set->weight * (1 + $reps / 30);
                }) ?? 0.0;
                return [
                    'x'      => $x,
                    'y'      => $y,
                    'bucket' => $hourToBucket[$s->created_at->hour] ?? 'Nacht',
                ];
            })->filter(fn($p) => $p['y'] > 0)->values();

            if ($points->count() < 4) {
                continue;
            }

            // Least-squares linear regression: y = slope·x + intercept
            $n     = $points->count();
            $sumX  = $points->sum('x');
            $sumY  = $points->sum('y');
            $sumXY = $points->sum(fn($p) => $p['x'] * $p['y']);
            $sumXX = $points->sum(fn($p) => $p['x'] * $p['x']);
            $denom = $n * $sumXX - $sumX * $sumX;

            if ($denom == 0) {
                $slope     = 0;
                $intercept = $sumY / $n;
            } else {
                $slope     = ($n * $sumXY - $sumX * $sumY) / $denom;
                $intercept = ($sumY - $slope * $sumX) / $n;
            }

            // Relative residual = (actual − expected) / expected × 100 %
            foreach ($points as $p) {
                $expected = $slope * $p['x'] + $intercept;
                if ($expected <= 0) {
                    continue;
                }
                $bucketDeltas[$p['bucket']][] = ($p['y'] - $expected) / $expected * 100;
            }
        }

        if (count($bucketDeltas) < 2) {
            return null;
        }

        // Aggregate per bucket
        $bucketStats = [];
        foreach (self::BUCKETS as $name => $def) {
            if (!isset($bucketDeltas[$name])) {
                continue;
            }
            $deltas = $bucketDeltas[$name];
            $bucketStats[$name] = [
                'avg_delta' => round(array_sum($deltas) / count($deltas), 1),
                'sessions'  => count($deltas),
                'range'     => $def['range'],
            ];
        }

        // Only consider buckets with ≥ 2 data points for the "best" label
        $eligible = array_filter($bucketStats, fn($b) => $b['sessions'] >= 2);
        if (count($eligible) < 2) {
            return null;
        }

        $best      = null;
        $bestDelta = PHP_FLOAT_MIN;
        foreach ($eligible as $name => $stats) {
            if ($stats['avg_delta'] > $bestDelta) {
                $bestDelta = $stats['avg_delta'];
                $best      = $name;
            }
        }

        // Normalize bar widths: remap [min, max] → [0, 100]
        $deltas  = array_column($bucketStats, 'avg_delta');
        $minD    = min($deltas);
        $maxD    = max($deltas);
        $range   = $maxD - $minD ?: 1;
        foreach ($bucketStats as $name => &$stats) {
            $stats['bar_pct'] = (int) round(($stats['avg_delta'] - $minD) / $range * 100);
        }
        unset($stats);

        // Persist PrimeTime on user record
        if ($best !== null && $user->prime_time !== $best) {
            $user->update(['prime_time' => $best]);
        }

        return [
            'buckets'    => $bucketStats,
            'best'       => $best,
            'best_range' => $best ? $bucketStats[$best]['range'] : null,
            'best_delta' => $best ? $bucketStats[$best]['avg_delta'] : null,
        ];
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
