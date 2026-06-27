<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class Club extends Model
{
    protected $table = 'clubs';

    protected $fillable = [
        'name',
        'description',
        'founded_date',
        'admin_user_id'
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(ClubMembership::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}