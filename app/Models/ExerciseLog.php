<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExerciseLog extends Model
{
    protected $fillable = ['exercise_id', 'weight', 'reps', 'logged_at'];

    protected $casts = [
        'logged_at' => 'datetime',
        'weight'    => 'decimal:2',
    ];

    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class);
    }
}
