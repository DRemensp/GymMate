<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeeklySchedule extends Model
{
    protected $fillable = ['user_id', 'day_of_week', 'label', 'is_rest'];

    protected $casts = ['is_rest' => 'boolean'];
}
