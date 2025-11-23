<?php
namespace App\Services;

class EmailNotificationService
{
   public function createNotification(array $props)
   {
      // este servicio va a servir para crear notificaciones email con mayor facilidad
      return array_merge([
         'name' => $props['name'] ?? '',
         'id' => $props['id'] ?? $props['name'] ?? '',
      ], $props);
   }
}
