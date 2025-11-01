<?php
namespace App\Services;

class ParametersValidationService
{
   public function __construct(...$args)
   {
      dd($args);
   }
   public function validateProps(array $props)
   {
      return array_merge([
         'name' => $props['name'] ?? '',
         'id' => $props['id'] ?? $props['name'] ?? '',
      ], $props);
   }
}
