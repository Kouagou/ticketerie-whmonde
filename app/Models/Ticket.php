<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'participant_id',
        'ticket_code',
        'is_used',
    ];

    protected $casts = [
        'is_used' => 'boolean',
    ];

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }
}
