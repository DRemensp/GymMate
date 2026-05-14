<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tour extends Model
{
    protected $fillable = [
        'user_id',
        'locations',
        'plans',
        'exercises',
        'logging',
        'weekly',
        'analyse',
        'profile',
        'cardio',
        'following',
        'settings',
        'data_export',
    ];

    protected $casts = [
        'locations'   => 'boolean',
        'plans'       => 'boolean',
        'exercises'   => 'boolean',
        'logging'     => 'boolean',
        'weekly'      => 'boolean',
        'analyse'     => 'boolean',
        'profile'     => 'boolean',
        'cardio'      => 'boolean',
        'following'   => 'boolean',
        'settings'    => 'boolean',
        'data_export' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
