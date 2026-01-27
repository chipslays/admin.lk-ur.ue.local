<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

class Ticket extends Model
{
    protected $connection = 'lk';

    protected $fillable = [
        'user_id',
        'title',
        'status',
        'email',
        'phone',
        'dog_number',
        'message',
        'notes',
        'attachments',
        'closed_at',
    ];

    protected $casts = [
        'closed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(Abonent::class, 'user_id', 'id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(TicketMessage::class, 'ticket_id', 'id')->oldest();
    }

    public function firstMessage(): HasOne
    {
        return $this->hasOne(TicketMessage::class, 'ticket_id', 'id')->oldestOfMany();
    }

    // public function getCreatedAtAttribute($value)
    // {
    //     return Carbon::parse($value)->format('d.m.Y H:i');
    // }

    // public function getClosedAtAttribute($value)
    // {
    //     return Carbon::parse($value)->format('d.m.Y H:i');
    // }
}
