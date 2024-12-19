<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\AgentNotification;

class AgentNotificationCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'message_template',
        'message_variables',
        'icon',
        'color',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'message_variables' => 'array',
        'is_active' => 'boolean',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_by = auth()->id();
        });

        static::updating(function ($model) {
            $model->updated_by = auth()->id();
        });
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function notifications()
    {
        return $this->hasMany(AgentNotification::class);
    }

}
