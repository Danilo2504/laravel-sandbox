<?php
namespace App\Enums;

enum NotificationStatus: string
{
    case PENDING = 'pending';
    case SENT = 'sent';
    case FAILED = 'failed'; 
    case SCHEDULED = 'scheduled';
    
    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pendiente',
            self::SENT => 'Enviado',
            self::FAILED => 'Fallido',
            self::SCHEDULED => 'Programado',
        };
    }
}

class NotificationStatusManager {
    private NotificationStatus $currentState;

    public function __construct(NotificationStatus $state) {
        $this->currentState = $state;
    }

    public function toSent(): void {
        if ($this->currentState === NotificationStatus::PENDING) {
            $this->currentState = NotificationStatus::SENT;
        }
    }

    public function toFailed(): void {
        if ($this->currentState === NotificationStatus::PENDING) {
            $this->currentState = NotificationStatus::FAILED;
        }
    }
    
    public function toScheduled(): void {
        if ($this->currentState === NotificationStatus::PENDING) {
            $this->currentState = NotificationStatus::SCHEDULED;
        }
    }

    public function getState(): NotificationStatus {
        return $this->currentState;
    }
}