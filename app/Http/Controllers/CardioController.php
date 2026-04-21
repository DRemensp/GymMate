<?php

namespace App\Http\Controllers;

use App\Models\CardioSession;
use Illuminate\Support\Facades\Auth;

class CardioController extends Controller
{
    public function index()
    {
        $user     = Auth::user();
        $sessions = CardioSession::where('user_id', $user->id)
            ->orderByDesc('logged_at')
            ->get();

        // Stats diese Woche
        $thisWeek = $sessions->filter(fn($s) => $s->logged_at->isCurrentWeek());
        $weeklyMinutes  = $thisWeek->sum('duration_minutes');
        $weeklyCount    = $thisWeek->count();
        $favoriteActivity = $sessions->groupBy('activity')
            ->map->count()
            ->sortDesc()
            ->keys()
            ->first();

        // Balken-Chart: letzte 8 Wochen
        $weeks = collect(range(7, 0))->map(function ($i) use ($sessions) {
            $start = now()->startOfWeek()->subWeeks($i);
            $end   = $start->copy()->endOfWeek();
            $label = $start->format('d.M');
            $mins  = $sessions
                ->filter(fn($s) => $s->logged_at->between($start, $end))
                ->sum('duration_minutes');
            return ['label' => $label, 'minutes' => $mins];
        });

        // Aktivitätsverteilung (letzte 30 Tage)
        $activityBreakdown = $sessions
            ->filter(fn($s) => $s->logged_at->gte(now()->subDays(30)))
            ->groupBy('activity')
            ->map(fn($group, $key) => [
                'label'   => CardioSession::ACTIVITIES[$key]['label'] ?? $key,
                'count'   => $group->count(),
                'minutes' => $group->sum('duration_minutes'),
            ])
            ->values();

        return view('cardio.index', compact(
            'sessions', 'weeklyMinutes', 'weeklyCount',
            'favoriteActivity', 'weeks', 'activityBreakdown'
        ));
    }

    public function destroy(CardioSession $cardioSession)
    {
        abort_if($cardioSession->user_id !== Auth::id(), 403);
        $cardioSession->delete();
        return back();
    }
}
