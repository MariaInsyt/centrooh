<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Region;
use App\Models\Billboard;

class District extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'status', 'region_id'];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    protected $casts = [
        'status' => 'boolean'
    ];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function billboards()
    {
        return $this->hasMany(Billboard::class);
    }
}
