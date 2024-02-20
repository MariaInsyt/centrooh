<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Region;

class District extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'status', 'region_id'];

    protected $casts = [
        'status' => 'boolean'
    ];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

}
