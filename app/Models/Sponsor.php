<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sponsor extends Model
{
    protected $table = 'sponsors';

    protected $fillable = [
        'name',
        'contact_person',
        'email',
        'phone',
        'address',
        'website',
        'description',
        'sponsor_type',
        'status',
    ];

    public function eventSponsors(): HasMany
    {
        return $this->hasMany(EventSponsor::class, 'sponsor_id');
    }
}