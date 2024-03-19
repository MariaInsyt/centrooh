<?php

namespace App\Models;

use App\Models\Agent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class Device extends SanctumPersonalAccessToken
{
    use HasApiTokens, HasFactory, SoftDeletes, Notifiable;

    protected $fillable = [
        'uuid',
        'device_name',
        'device_type',
        'device_brand',
        'token',
        'notification_token',
        'ip_address',
        'is_active',
        'agent_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function routeNotificationForFcm()
    {
        return $this->notification_token;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }
}
