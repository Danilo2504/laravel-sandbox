<?php

namespace App\Models;

use App\Enums\NotificationStatus;
use App\Enums\NotificationStatusManager;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $maxAttempts = 5;
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
        'status' => NotificationStatus::class,
        'recipients' => 'array',
        'metadata' => 'array',
        'priority' => 'integer',
        'attempts' => 'integer',
        'sent_at' => 'datetime',
        'failed_at' => 'datetime'
    ];

    /* Scopes */
    #[Scope]
    protected function sent(Builder $query): Builder
    {
        return $query->where('status', NotificationStatus::SENT);
    }
    #[Scope]
    protected function pending(Builder $query): Builder
    {
        return $query->where('status', NotificationStatus::PENDING);
    }
    #[Scope]
    protected function failed(Builder $query): Builder
    {
        return $query->where('status', NotificationStatus::FAILED);
    }
    #[Scope]
    protected function byStatus(Builder $query, NotificationStatus|string $status): Builder
    {
        return $query->where('status', $status);
    }
    #[Scope]
    protected function byType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }
    #[Scope]
    protected function withFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['status'] ?? null, fn($q, $status) => $q->byStatus($status))
            ->when($filters['type'] ?? null, fn($q, $type) => $q->byType($type))
            ->when($filters['from'] ?? null, fn($q, $from) => $q->where('from', $from));
    }

    /* Methods */
    public function canRetry(): bool
    {
        if($this->is_failed && $this->attempts <= $this->maxAttempts){
            return true;
        }

        return false;
    }
    public function markAsSent(){
        $state = new NotificationStatusManager($this->status);
        $state->toSent();
        
        $this->status = $state->getState()->value;
        $this->sent_at = now();

        $this->save();
    }
    public function markAsFailed(?string $error){
        $state = new NotificationStatusManager($this->status);
        $state->toFailed();
        
        $this->status = $state->getState()->value;
        $this->attempts += 1;
        $this->failed_at = now();
        
        if($error && trim($error) !== ''){
            $this->error = $error;
        }

        $this->save();
    }
    
    /* Accessors */
    protected function isPending(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => NotificationStatus::tryFrom($attributes['value']) === NotificationStatus::PENDING
        );
    }
    protected function isSent(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => NotificationStatus::tryFrom($attributes['value']) === NotificationStatus::SENT
        );
    }
    protected function isFailed(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => NotificationStatus::tryFrom($attributes['value']) === NotificationStatus::FAILED
        );
    }
    protected function attemptsRemaining(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                if ($attributes['attempts'] >= $this->maxAttempts) {
                    return 0;
                }

                return $this->maxAttempts - (int) $attributes['attempts'];
            } 
        );
    }

    /* Relations */
    public function notifiable()
    {
        return $this->morphTo();
    }
}
