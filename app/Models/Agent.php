<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\BillboardAgent;

class Agent extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'username',
        'profile_picture',
        'email',
        'password',
        'phone_number',
        'status',
        'email_verified_at'
    ];

    protected $hidden = [
        'password'
    ];

    protected $casts = [
        'status' => 'boolean',
        'password' => 'hashed',
    ];

    public function billboards()
    {
        return $this->hasMany(BillboardAgent::class);
    }
}
