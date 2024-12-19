<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OneTimePassword extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'phone_number',
        'status',
        'phone_number_verified_at',
        'expires_at'
    ];

    protected $hidden = [
        'deleted_at',
    ];

    protected $casts = [
        'phone_number_verified_at' => 'datetime',
        'expires_at' => 'datetime'
    ];
}
