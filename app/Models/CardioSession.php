<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CardioSession extends Model
{
    protected $fillable = [
        'user_id', 'activity', 'duration_minutes', 'distance_km',
        'intensity', 'hiit_rounds', 'hiit_work_seconds', 'hiit_rest_seconds',
        'notes', 'calories_burned', 'logged_at',
    ];

    protected $casts = [
        'logged_at'   => 'datetime',
        'distance_km' => 'decimal:2',
    ];

    const MET_VALUES = [
        'laufband'     => ['leicht' => 6.0,  'mittel' => 8.5,  'intensiv' => 11.0],
        'fahrrad'      => ['leicht' => 5.0,  'mittel' => 7.5,  'intensiv' => 10.0],
        'rudergeraet'  => ['leicht' => 4.5,  'mittel' => 7.0,  'intensiv' => 9.5],
        'stairmaster'  => ['leicht' => 5.5,  'mittel' => 8.0,  'intensiv' => 10.5],
        'crosstrainer' => ['leicht' => 5.0,  'mittel' => 7.0,  'intensiv' => 9.0],
        'seilspringen' => ['leicht' => 8.0,  'mittel' => 10.0, 'intensiv' => 12.0],
        'hiit'         => ['leicht' => 8.0,  'mittel' => 10.0, 'intensiv' => 12.0],
    ];

    const ACTIVITIES = [
        'laufband'     => ['label' => 'Laufband',     'has_distance' => true,  'is_hiit' => false],
        'fahrrad'      => ['label' => 'Fahrrad',      'has_distance' => true,  'is_hiit' => false],
        'rudergeraet'  => ['label' => 'Rudergerät',   'has_distance' => true,  'is_hiit' => false],
        'stairmaster'  => ['label' => 'Stairmaster',  'has_distance' => false, 'is_hiit' => false],
        'crosstrainer' => ['label' => 'Cross-Trainer','has_distance' => false, 'is_hiit' => false],
        'seilspringen' => ['label' => 'Seilspringen', 'has_distance' => false, 'is_hiit' => false],
        'hiit'         => ['label' => 'HIIT',         'has_distance' => false, 'is_hiit' => true],
    ];

    public static function calculateCalories(
        string $activity,
        string $intensity,
        int $durationMinutes,
        float $weightKg
    ): int {
        $met = self::MET_VALUES[$activity][$intensity] ?? null;
        if ($met === null) return 0;
        return (int) round($met * $weightKg * ($durationMinutes / 60));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function activityLabel(): string
    {
        return self::ACTIVITIES[$this->activity]['label'] ?? $this->activity;
    }
}
