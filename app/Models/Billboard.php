<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\District;
use App\Models\User;
use App\Models\BillboardImage;
use App\Models\Agent;
use App\Models\AgentDistrict;
class Billboard extends Model
{
    use HasFactory, SoftDeletes;

    public static function boot()
    {
        parent::boot();
        static::creating(function ($billboard) {
            $billboard->created_by = auth()->id();
        });
    }

    protected $fillable = [
        'district_id',
        'created_by',
        'name',
        'status',
        'agent_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    //Scope Active
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function images()
    {
        return $this->hasMany(BillboardImage::class);
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }
}
