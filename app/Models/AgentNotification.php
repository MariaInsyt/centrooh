<?php

namespace App\Models;

use App\Models\Agent;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgentNotification extends Model
{
    use HasFactory, SoftDeletes;

    public static function boot()
    {
        parent::boot();
        static::creating(function ($notification) {
            $notification->user_id = auth()->id();
        });
    }

    //Todo: Make prunable

    protected $fillable = [
        'agent_id',
        'user_id',
        'title',
        'message',
        'read_at',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
        'user_id',
        // 'agent_id',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    protected $appends = [
        'created_at_human',
    ];

    public function getCreatedAtHumanAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }
}
