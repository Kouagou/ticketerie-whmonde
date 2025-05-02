<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'status',
        'max_participants',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    public function isFullyBooked()
    {
        return $this->participants()->count() >= $this->max_participants;
    }

    public function getAvailableSpotsAttribute()
    {
        return $this->max_participants - $this->participants()->count();
    }

    public function getCurrentParticipantsAttribute()
    {
        return $this->participants()->count();
    }
}
