<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $table = 'certificates';

    protected $fillable = [
        'user_id',
        'event_id',
        'issued_by',
        'certificate_number',
        'verification_code',
        'certificate_type',
        'title',
        'description',
        'issued_at',
        'revoked_at',
        'status',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];
}