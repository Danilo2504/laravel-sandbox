<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

abstract class Tool extends Model {

   protected $validator = null;
   protected $errors = null;
   protected $rules = array();

   public static function boot()
   {
      parent::boot();
      static::creating(function($model)
      {
         // if(!$this->isvalid($model->toArray(), $this->rules)) return false;
         logger()->warning('Creating instance of model ' . $model::class);
         logger()->warning('Model ID ' . $model->id);
      });
   }

   // protected function isvalid($inputs, $rules)
   // {
   //    // return true/false
   //    $this->validator = Validator::make($inputs, $rules);
   //    if($this->validator->passes()) return true;
   //    else {
   //       $this->errors = $this->validator->errors();
   //       return false;
   //    }
   // }

   // public function getErrors()
   // {
   //    return $this->errors;
   // }

   // public function hasErrors()
   // {
   //    return count($this->errors);
   // }
}