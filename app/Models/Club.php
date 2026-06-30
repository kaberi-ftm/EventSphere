<?php
namespace App\Models;

use App\Models\ClubMembership;
use App\Models\Event;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\User;

class Club extends Model
{
    protected $fillable = [
        'name',
        'description',
        'founded_date',
        'admin_user_id'
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function membership(): HasMany
    {
        return $this->hasMany(ClubMembership::class);
    }
     public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}
