<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\District;
use App\Models\User;

class Billboard extends Model
{
    use HasFactory, SoftDeletes;

    //Automatically add user_id to created_by on save
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
    ];

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
}
