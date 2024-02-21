<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BillboardImage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'billboard_id',
        'image',
    ];

    public function billboard()
    {
        return $this->belongsTo(Billboard::class);
    }
}
