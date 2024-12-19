<?php

namespace App\Models;

use App\Models\Billboard;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use App\Observers\BillboardImageObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy(BillboardImageObserver::class)]
class BillboardImage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'billboard_id',
        'image',
        'is_active',
    ];

    protected $appends = ['image_url'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function getImageUrlAttribute()
    {
        return $this->image ? Storage::url($this->image) : null;
    }

    public function lastest()
    {
        return $this->active()->latest()->first();
    }

    public function billboard()
    {
        return $this->belongsTo(Billboard::class);
    }
}
