<?php

namespace App\Models;

use App\Models\Billboard;
use App\Models\AgentDistrict;
use App\Models\Device;
use App\Models\AgentNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
class Agent extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'uuid',
        'username',
        'profile_picture',
        'email',
        'phone_number',
        'status',
        'email_verified_at',
    ];

    protected $hidden = [
        'created_at',
        'deleted_at',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    protected $appends = ['profile_picture_url'];

    public function getProfilePictureUrlAttribute()
    {
        return $this->profile_picture ? Storage::url($this->profile_picture) : null;
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeWithDistricts($query)
    {
        return $query->where('status', true)->whereHas('agentDistricts');
    }

    public function billboards()
    {
        return $this->hasMany(Billboard::class);
    }

    public function agentDistricts()
    {
        return $this->hasMany(AgentDistrict::class);
    }

    public function devices()
    {
        return $this->hasMany(Device::class);
    }

    public function notifications()
    {
        return $this->hasMany(AgentNotification::class);
    }
}
