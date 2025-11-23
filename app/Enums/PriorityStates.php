<?php
namespace App\Enums;

enum PriorityStates: int
{
    case URGENT = 1;
    case HIGH = 2;
    case NORMAL = 3;
    case LOW = 4; 
    case BULK = 5; 
    
    public function label(): string
    {
        return match($this) {
            self::URGENT => 'Urgente',
            self::HIGH => 'Alta',
            self::NORMAL => 'Normal',
            self::LOW => 'Baja',
            self::BULK => 'Masiva',
        };
    }
}

class PriorityStatesManager {
    private PriorityStates $currentPriority;

    public function __construct(PriorityStates $state) {
        $this->currentPriority = $state;
    }

    public function toUrgent(): void {
        $this->currentPriority = PriorityStates::URGENT;
    }

    public function toHigh(): void {
        $this->currentPriority = PriorityStates::HIGH;
    }

    public function toNormal(): void {
        $this->currentPriority = PriorityStates::NORMAL;
    }

    public function toLow(): void {
        $this->currentPriority = PriorityStates::LOW;
    }

    public function toBulk(): void {
        $this->currentPriority = PriorityStates::BULK;
    }

    public function getPriority(): PriorityStates {
        return $this->currentPriority;
    }
}