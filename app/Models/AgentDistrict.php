<?php

namespace App\Models;

use App\Models\Agent;
use App\Models\District;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class AgentDistrict extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'agent_id',
        'district_id',
        'is_active',
        'is_primary',
    ];

    //Hidden fields
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
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
