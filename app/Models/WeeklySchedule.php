<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class WeeklySchedule extends Model
{
    protected $fillable = ['user_id', 'day_of_week', 'label', 'is_rest'];

    protected $casts = ['is_rest' => 'boolean'];

    public function exercises(): BelongsToMany
    {
        return $this->belongsToMany(Exercise::class, 'weekly_schedule_exercises');
    }
}
