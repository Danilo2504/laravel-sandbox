<?php

namespace App\Models;

use App\Enums\NotifactionStatus;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications';
    protected $fillable = [
        'notificable_type',
        'notificable_id',
        'status',
        'from',
        'recipients',
        'reply_to',
        'subject',
        'error',
        'type',
        'template',
        'body',
        'metadata',
        'priority',
        'attempts',
        'sent_at',
        'failed_at',
        'created_at',
        'updated_at'
    ];
    protected $casts = [
        'status' => NotifactionStatus::class,
        'recipients' => 'array',
        'metadata' => 'array',
        'priority' => 'integer',
        'attempts' => 'integer',
        'sent_at' => 'datetime',
        'failed_at' => 'datetime'
    ];
}
