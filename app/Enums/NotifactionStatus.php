<?php
namespace App\Enums;

enum NotifactionStatus: string
{
    case PENDING = 'pending';
    case SENT = 'sent';
    case FAILED = 'failed'; 
}

// revisar que tipo de utilidades podemos aprovechar de los enums
