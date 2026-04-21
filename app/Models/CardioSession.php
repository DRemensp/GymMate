<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CardioSession extends Model
{
    protected $fillable = [
        'user_id', 'activity', 'duration_minutes', 'distance_km',
        'intensity', 'hiit_rounds', 'hiit_work_seconds', 'hiit_rest_seconds',
        'notes', 'logged_at',
    ];

    protected $casts = [
        'logged_at'   => 'datetime',
        'distance_km' => 'decimal:2',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function activityLabel(): string
    {
        return self::ACTIVITIES[$this->activity]['label'] ?? $this->activity;
    }
}
