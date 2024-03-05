<?php

namespace App\Models;

use App\Models\District;
use App\Models\User;
use App\Models\BillboardImage;
use App\Models\Agent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        'lat',
        'lng',
        'location',
        'address',
    ];

    protected $hidden = [
        'created_at',
        'deleted_at',
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

    /**
     * Get the lat and lng attribute/field names used on this table
     *
     * Used by the Filament Google Maps package.
     *
     * @return string[]
     */
    public static function getLatLngAttributes(): array
    {
        return [
            'lat' => 'lat',
            'lng' => 'lng',
        ];
    }

   /**
    * Get the name of the computed location attribute
    *
    * Used by the Filament Google Maps package.
    *
    * @return string
    */
    public static function getComputedLocation(): string
    {
        return 'location';
    }
}
