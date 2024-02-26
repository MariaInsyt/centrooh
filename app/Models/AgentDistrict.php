<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Agent;
use App\Models\District;
use App\Models\Billboard;

class AgentDistrict extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'agent_id',
        'district_id',
        'is_active',
        'is_primary',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_primary' => 'boolean',
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }
}
