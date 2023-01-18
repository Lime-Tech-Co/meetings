<?php

namespace Modules\V1\Users\Models;

use App\Http\Traits\Uuids;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Modules\V1\Meetings\Models\EmployeeBusyTime;
use Modules\V1\Users\Models\Constants\UserStatus;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use Uuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'external_user_id',
        'full_name',
        'email',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * @return HasMany
     */
    public function busyTimes(): HasMany
    {
        return $this->hasMany(EmployeeBusyTime::class, 'external_user_id', 'external_user_id')
            ->where('busy_at', '>', now());
    }

    /**
     * @param       $query
     * @param array $userIds
     *
     * @return Builder
     */
    public function scopeGetParticipants($query, array $userIds): Builder
    {
        return $query->whereIn('external_user_id', $userIds)->active();
    }

    /**
     * @param       $query
     * @param array $userIds
     *
     * @return Builder
     */
    public function scopeActive($query): Builder
    {
        return $query->where('status', UserStatus::ENABLED->value);
    }
}
