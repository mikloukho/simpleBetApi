<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Result extends Model
{
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'score' => 'array',
    ];

    /**
     * Get the event the result belongs to.
     *
     * @return BelongsTo
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the winning team.
     *
     * @return BelongsTo
     */
    public function winner(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
