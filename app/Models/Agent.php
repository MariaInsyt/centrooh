<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Billboard;
use App\Models\AgentDistrict;
use App\Models\Device;

class Agent extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'username',
        'profile_picture',
        'email',
        'phone_number',
        'status',
        'email_verified_at'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

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
}
