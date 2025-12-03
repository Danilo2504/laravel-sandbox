<?php

namespace App\Models;

use App\Enums\NotificationStatus;
use App\Enums\NotificationStatusManager;
use App\Enums\PriorityStates;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

/**
 * Notification Model
 *
 * @property int $id
 * @property string|null $notificable_type
 * @property int|null $notificable_id
 * @property NotificationStatus $status
 * @property bool $is_debug
 * @property string|null $from
 * @property array $recipients
 * @property string|null $reply_to
 * @property string|null $subject
 * @property string|null $error_message
 * @property string|null $type
 * @property string|null $template_name
 * @property array|null $template_data
 * @property string|null $message
 * @property array|null $metadata
 * @property PriorityStates $priority
 * @property int $attempts
 * @property int $max_attempts
 * @property \Illuminate\Support\Carbon|null $sent_at
 * @property \Illuminate\Support\Carbon|null $scheduled_at
 * @property \Illuminate\Support\Carbon|null $failed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @property-read bool $is_pending
 * @property-read bool $is_sent
 * @property-read bool $is_failed
 * @property-read bool $is_scheduled
 * @property-read bool $is_debug
 * @property-read string $priority_label
 * @property-read int $attempts_remaining
 *
 * @property-read \Illuminate\Database\Eloquent\Model|null $notifiable
 *
 * @method static \Illuminate\Database\Eloquent\Builder|static sent()
 * @method static \Illuminate\Database\Eloquent\Builder|static pending()
 * @method static \Illuminate\Database\Eloquent\Builder|static failed()
 * @method static \Illuminate\Database\Eloquent\Builder|static scheduled()
 * @method static \Illuminate\Database\Eloquent\Builder|static byStatus(NotificationStatus|string $status)
 * @method static \Illuminate\Database\Eloquent\Builder|static byType(string $type)
 * @method static \Illuminate\Database\Eloquent\Builder|static byPriority(PriorityStates|int $priority)
 * @method static \Illuminate\Database\Eloquent\Builder|static orderByPriority(string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|static withFilters(array $filters)
 */
class Notification extends Model
{
    protected $table = 'notifications';
    protected $fillable = [
        'notificable_type',
        'notificable_id',
        'status',
        'is_debug',
        'from',
        'recipients',
        'reply_to',
        'subject',
        'error_message',
        'type',
        'template_name',
        'template_data',
        'message',
        'metadata',
        'priority',
        'attempts',
        'max_attempts',
        'sent_at',
        'scheduled_at',
        'failed_at',
        'created_at',
        'updated_at'
    ];
    protected $casts = [
        'status' => NotificationStatus::class,
        'priority' => PriorityStates::class,
        'is_debug' => 'boolean',
        'recipients' => 'array',
        'metadata' => 'array',
        'template_data' => 'array',
        'attempts' => 'integer',
        'max_attempts' => 'integer',
        'sent_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'failed_at' => 'datetime'
    ];

    /* Scopes */
    /**
     * Scope a query to only include sent notifications.
     *
     * @param Builder $query
     * @return Builder
     */
    #[Scope]
    protected function sent(Builder $query): Builder
    {
        return $query->where('status', NotificationStatus::SENT->value);
    }
    /**
     * Scope a query to only include pending notifications.
     *
     * @param Builder $query
     * @return Builder
     */
    #[Scope]
    protected function pending(Builder $query): Builder
    {
        return $query->where('status', NotificationStatus::PENDING->value);
    }
    /**
     * Scope a query to only include failed notifications.
     *
     * @param Builder $query
     * @return Builder
     */
    #[Scope]
    protected function failed(Builder $query): Builder
    {
        return $query->where('status', NotificationStatus::FAILED->value);
    }
    /**
     * Scope a query to only include scheduled notifications.
     *
     * @param Builder $query
     * @return Builder
     */
    #[Scope]
    protected function scheduled(Builder $query): Builder
    {
        return $query->where('status', NotificationStatus::SCHEDULED->value);
    }
    /**
     * Scope a query to filter notifications by status.
     *
     * @param Builder $query
     * @param NotificationStatus|string $status
     * @return Builder
     */
    #[Scope]
    protected function byStatus(Builder $query, NotificationStatus|string $status): Builder
    {
        return $query->where('status', $status instanceof NotificationStatus ? $status->value : $status);
    }
    /**
     * Scope a query to filter notifications by type.
     *
     * @param Builder $query
     * @param string $type
     * @return Builder
     */
    #[Scope]
    protected function byType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }
    /**
     * Scope a query to filter notifications by priority for Queue.
     *
     * @param Builder $query
     * @param PriorityStates|int $priority
     * @return Builder
     */
    #[Scope]
    protected function byPriority(Builder $query, PriorityStates|int $priority): Builder
    {
        return $query->where('priority', $priority instanceof PriorityStates ? $priority->value : $priority);
    }
    /**
     * Scope a query to sort notifications by priority in a given direction.
     *
     * @param Builder $query
     * @param string $direction = 'asc'
     * @return Builder
     */
    #[Scope]
    protected function orderByPriority($query, string $direction = 'asc')
    {
        return $query->orderBy('priority', $direction);
    }
    /**
     * Scope a query to apply multiple filters.
     *
     * @param Builder $query
     * @param array $filters Filters array with keys: status, type, from
     * @return Builder
     */
    #[Scope]
    protected function withFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['status'] ?? null, fn($q, $status) => $q->byStatus($status))
            ->when($filters['is_debug'] ?? null, fn($q, $is_debug) => $q->where('is_debug', $is_debug))
            ->when($filters['type'] ?? null, fn($q, $type) => $q->byType($type))
            ->when($filters['from'] ?? null, fn($q, $from) => $q->where('from', $from))
            ->when($filters['priority'] ?? null, fn($q, $priority) => $q->byPriority($priority));
    }

    /* Methods */
    /**
     * Check if the notification can be retried.
     *
     * @return bool
     */
    public function canRetry(): bool
    {
        if($this->is_failed && $this->attempts <= $this->maxAttempts){
            return true;
        }

        return false;
    }
    /**
     * Mark the notification as sent.
     *
     * @return void
     */
    public function markAsSent(){
        $state = new NotificationStatusManager($this->status);
        $state->toSent();
        
        $this->status = $state->getState()->value;
        $this->sent_at = now();

        $this->save();
    }
    /**
     * Mark the notification as failed.
     *
     * @param string|null $error Error message
     * @return void
     */
    public function markAsFailed(?string $error){
        $state = new NotificationStatusManager($this->status);
        $state->toFailed();
        
        $this->status = $state->getState()->value;
        $this->attempts += 1;
        $this->failed_at = now();
        
        if($error && trim($error) !== ''){
            $this->error_message = $error;
        }

        $this->save();
    }
    /**
     * Mark the notification as scheduled.
     *
     * @param PriorityStates|null $priority Priority level
     * @return void
     */
    public function markAsScheduled(?PriorityStates $priority){
        $state = new NotificationStatusManager($this->status);
        $state->toScheduled();
        
        $this->status = $state->getState()->value;
        $this->scheduled_at = now();
        $this->priority = $priority?->value ?? PriorityStates::NORMAL->value;

        $this->save();
    }
    
    /* Accessors */
    /**
     * Accessor to check if the notification is pending.
     *
     * @return Attribute
     */
    protected function isPending(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => NotificationStatus::tryFrom($attributes['value']) === NotificationStatus::PENDING
        );
    }
    /**
     * Accessor to check if the notification is sent.
     *
     * @return Attribute
     */
    protected function isSent(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => NotificationStatus::tryFrom($attributes['value']) === NotificationStatus::SENT
        );
    }
    /**
     * Accessor to check if the notification has failed.
     *
     * @return Attribute
     */
    protected function isFailed(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => NotificationStatus::tryFrom($attributes['value']) === NotificationStatus::FAILED
        );
    }
    /**
     * Accessor to check if the notification is scheduled.
     *
     * @return Attribute
     */
    protected function isScheduled(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => NotificationStatus::tryFrom($attributes['value']) === NotificationStatus::SCHEDULED
        );
    }
    /**
     * Accessor to check if the notification is debug.
     *
     * @return Attribute
     */
    protected function isDebug(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => (bool) $attributes['is_debug']
        );
    }
    /**
     * Accessor to get the priority label.
     *
     * @return Attribute
     */
    protected function priorityLabel(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => PriorityStates::tryFrom($attributes['priority'])?->label() ?? $value
        );
    }
    /**
     * Accessor to get the number of attempts remaining.
     *
     * @return Attribute
     */
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
    /**
     * Get the parent notifiable model (user, order, etc.).
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function notificable()
    {
        return $this->morphTo();
    }
}
