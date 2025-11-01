<?php

namespace App\View\Components\Base;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use Illuminate\View\Component;

abstract class BaseComponent extends Component
{
   // Propiedades base
   public $name;
   public $id;
   public $type;
   public $label;
   public $noLabel;
   public $value;
   public $cssClasses;
   public $required;
   public $disabled;
   public $readonly;
   public $placeholder;

   protected $model;
   protected $field;

   /**
    * Constructor con todos los parámetros posibles
    * Laravel bindeará automáticamente las props de Blade a estos parámetros
    */
   public function __construct(
      $name = '',
      $id = '',
      $type = '',
      $label = '',
      $noLabel = false,
      $value = '',
      $cssClasses = [],
      $readonly = false,
      $required = false,
      $disabled = false,
      $placeholder = ''
   )
   {  
      $baseParameters = compact('name', 'id', 'type', 'label', 'noLabel', 'value', 'cssClasses', 'readonly', 'required', 'disabled', 'placeholder');
      $this->initializeBaseProperties($baseParameters);
   }

   /**
    * Inicializar propiedades base del componente
    */
   protected function initializeBaseProperties(array $params): void
   {
      // Name (requerido)
      $name = Arr::get($params, 'name', '');
      if (trim($name) === '') {
         throw new \InvalidArgumentException('El parámetro "name" es requerido');
      }
      $this->name = $name;

      // ID (default: name)
      $this->id = trim(Arr::get($params, 'id', '') !== '') ? Arr::get($params, 'id', '') : $name;

      // Type
      $this->type = Arr::get($params, 'type', '');

      // Value
      $this->value = Arr::get($params, 'value', '');

      // Required
      $this->required = Arr::get($params, 'required', false);

      // Disabled
      $this->disabled = Arr::get($params, 'disabled', false);

      // Readonly
      $this->readonly = Arr::get($params, 'readonly', false);

      // Placeholder
      $this->placeholder = Arr::get($params, 'placeholder', '');

      // Label
      $this->noLabel = Arr::get($params, 'noLabel', false);
      if (!$this->noLabel) {
         $label = Arr::get($params, 'label', '');
         if (trim($label) === '') {
            throw new \InvalidArgumentException('El parámetro "label" es requerido cuando noLabel es false');
         }
         $this->label = $label;
      } else {
         $this->label = Arr::get($params, 'label', '');
      }

      // CSS Classes
      $cssClasses = Arr::get($params, 'cssClasses');
      if ($cssClasses) {
         if (is_string($cssClasses)) {
            $this->cssClasses = $cssClasses;
         } elseif (is_array($cssClasses)) {
            $this->cssClasses = Arr::toCssClasses($cssClasses);
         } else {
            throw new \InvalidArgumentException('El parámetro "cssClasses" debe ser de tipo array o string');
         }
      }
   }

   // Getters
   public function getName()
   {
      return $this->name;
   }

   public function getId()
   {
      return $this->id;
   }

   public function getType(){
      return $this->type;
   }
   
   public function getValue()
   {
      return $this->value;
   }

   public function getLabel()
   {
      return $this->label;   
   }

   protected function getModel(?string $key = null)
   {
      if(!$this->model){
         return null;
      }

      return $key ? $this->model->get($key) : $this->model;
   }

   protected function setModel($model)
   {
      if ($model) {
         if ($model instanceof Model || $model instanceof Collection) {
            $this->model = $model;
         } elseif (is_array($model) || is_object($model)) {
            $model = (object) $model;
            $this->model = collect($model);
         } else {
            throw new \InvalidArgumentException(
               'El parámetro "model" debe ser de tipo Model, Collection, array u objeto.'
            );
         }
      }

      if($this->model->get($this->name)){
         $this->value = $this->model->get($this->name);
      }

      return $this->model;
   }
}
