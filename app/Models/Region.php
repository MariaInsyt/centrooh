<?php

namespace App\Models;

use App\Models\District;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Region extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'status'];

    protected $casts = [
        'status' => 'boolean'
    ];

    public function districts()
    {
        return $this->hasMany(District::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
    
}
