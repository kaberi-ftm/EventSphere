<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Club;

class Event extends Model
{
    protected $fillable = [
        'club_id',
        'venue_id',
        'created_by',
        'title',
        'description',
        'start_time',
        'end_time',
        'status',
        'max_participants'
    ];

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }
}