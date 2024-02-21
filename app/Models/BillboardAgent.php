<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Billboard;
use App\Models\Agent;

class BillboardAgent extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'billboard_id',
        'agent_id',
    ];

    public function billboard()
    {
        return $this->belongsTo(Billboard::class);
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }
}
