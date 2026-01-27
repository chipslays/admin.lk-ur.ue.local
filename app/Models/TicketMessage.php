<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class TicketMessage extends Model
{
    protected $connection = 'lk';

    protected $fillable = [
        'ticket_id',
        'user_id',
        'message',
        'attachments',
        'manager_name',
    ];

    protected $casts = [
        'attachments' => 'array',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(Abonent::class, 'user_id', 'id');
    }

    // public function getCreatedAtAttribute($value)
    // {
    //     return Carbon::parse($value)->format('d.m.Y H:i');
    // }
}
