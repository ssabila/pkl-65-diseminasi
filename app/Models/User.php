<?php

namespace App\Models;


use App\Models\Riset;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements Auditable
{
    use HasApiTokens, HasFactory, Notifiable, TwoFactorAuthenticatable;
    use \OwenIt\Auditing\Auditable;
    use HasRoles;


    protected $guarded = ['id'];

    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
        'password_expiry_at',
        'password_changed_at',
        'force_password_change',
        'disable_account',
        'riset_id',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'password_expiry_at' => 'datetime',
        'password_changed_at' => 'datetime',
        'force_password_change' => 'boolean',
        'disable_account' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    protected $appends = ['created_at_formatted'];

    public function formatDateStyle(?Carbon $date = null): string
    {
        $date = $date ?? $this->created_at;

        if (!$date) {
            return '';
        }

        if ($date->diffInMinutes() < 5) {
            return 'Just now';
        }

        if ($date->isToday()) {
            return $date->diffForHumans(['short' => false, 'parts' => 1]);
        }

        if ($date->isYesterday()) {
            return 'Yesterday';
        }

        if ($date->isCurrentYear()) {
            return $date->format('F j');
        }

        return $date->format('F j, Y');
    }


    public function getCreatedAtFormattedAttribute(): string
    {
        return $this->formatDateStyle($this->created_at);
    }



    public function riset()
    {
        return $this->belongsTo(Riset::class);
    }

    public function loginHistory()
    {
        return $this->morphMany(LoginHistory::class, 'user');
    }


    public function latestLogin()
    {
        return $this->morphOne(LoginHistory::class, 'user')->latestOfMany('login_at');
    }


    public function isLoggedIn(): bool
    {
        return $this->latestLogin?->logout_at === null;
    }


    public function isSuperUser(): bool
    {
        return $this->hasRole('superuser');
    }


    public function canBeDeleted(): bool
    {
        return !$this->isSuperUser();
    }
}
