<?php

namespace App\Models;

use Carbon\Carbon;
use Chiiya\FilamentAccessControl\Contracts\AccessControlUser;
use Chiiya\FilamentAccessControl\Notifications\TwoFactorCode;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Filament\Models\Contracts\FilamentUser as FilamentUserInterface;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements AccessControlUser, FilamentUserInterface
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'expires_at',
        'two_factor_code',
        'two_factor_expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'expires_at' => 'datetime',
        'two_factor_expires_at' => 'datetime',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return str_ends_with($this->email, '@insytmedia.com');
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super-admin');
    }

    public function isExpired(): bool
    {
        return $this->expires_at instanceof Carbon && now()->gt($this->expires_at);
    }

    public function extend(): void
    {
        $this->update([
            'expires_at' => now()->addMonths(6)->endOfDay(),
        ]);
    }

    public function hasTwoFactorCode(): bool
    {
        return $this->getTwoFactorCode() !== null;
    }

    public function getTwoFactorCode(): ?string
    {
        return $this->two_factor_code;
    }

    public function twoFactorCodeIsExpired(): bool
    {
        return $this->two_factor_expires_at instanceof Carbon && now()->gt($this->two_factor_expires_at);
    }

    public function sendTwoFactorCodeNotification(): void
    {
        $this->notify(new TwoFactorCode);
    }
}
