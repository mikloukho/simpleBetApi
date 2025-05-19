<?php

namespace App\Models;

use App\Enums\EventStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'sport_id',
        'start_at',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'status' => EventStatus::class,
        'start_at' => 'datetime',
    ];

    /**
     * Get the sport of the event.
     *
     * @return BelongsTo
     */
    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class);
    }

    /**
     * Get all teams participating in the event.
     *
     * @return BelongsToMany
     */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'event_team')
            ->withTimestamps();
    }

    /**
     * Get all results of the event.
     *
     * @return HasMany
     */
    public function results(): HasMany
    {
        return $this->hasMany(Result::class);
    }

    /**
     * Get odds for the event.
     *
     * @return HasMany
     */
    public function odds(): HasMany
    {
        return $this->hasMany(Odd::class)->whereNull('deleted_at');
    }

    /**
     * Get all odds for the event.
     *
     * @return HasMany
     */
    public function allOdds()
    {
        return $this->hasMany(Odd::class)->withTrashed();
    }

    public function scopeWithTeamsMatching($query, array $slugs)
    {
        return $query->whereHas('teams', function ($q) use ($slugs) {
            $q->whereIn('slug', $slugs);
        }, '=', count($slugs));
    }


}
