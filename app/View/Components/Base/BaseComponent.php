<?php

namespace App\View\Components\Base;

use Closure;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View as ViewContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use Illuminate\View\Component;
use ReflectionClass;

abstract class BaseComponent extends Component
{
   // Propiedades base
   public $name;
   public $id;
   public $value;
   public $label;
   public $noLabel;
   public $required;
   public $disabled;
   public $readonly;
   public $cssClasses;
   public $type;
   public $allowedTypes;
   public $options;
   protected $model;
   protected $extraParams;
   protected $field;

   /**
    * Constructor con todos los parámetros posibles
    * Laravel bindeará automáticamente las props de Blade a estos parámetros
    */
   public function __construct(
      $name = '',
      $id = '',
      $value = '',
      $label = '',
      $noLabel = false,
      $required = false,
      $disabled = false,
      $readonly = false,
      $cssClasses = null,
      $type = null,
      $allowedTypes = null,
      $options = null
   )
   {  
      // Crear array con todos los parámetros
      $params = compact(
         'name', 'id', 'value', 'label', 'noLabel',
         'required', 'disabled', 'readonly', 'cssClasses',
         'type', 'allowedTypes', 'options'
      );

      // Inicializar propiedades base
      $this->initializeBaseProperties($params);
      $field = new ReflectionClass($this);
      $this->field = $field->name;
   }

   /**
    * Método para inicialización personalizada en clases hijas
    * Las clases hijas pueden sobrescribir este método para agregar lógica adicional
    */
   protected function afterInitialize(array $params): array
   {
      // Las clases hijas pueden sobrescribir este método
      return [];
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
      $this->id = Arr::get($params, 'id', '') ?: $name;

      // Value
      $this->value = Arr::get($params, 'value', '');

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

      // Atributos de estado
      $this->required = Arr::get($params, 'required', false);
      $this->disabled = Arr::get($params, 'disabled', false);
      $this->readonly = Arr::get($params, 'readonly', false);

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

      // Type y AllowedTypes
      $allowedTypes = Arr::get($params, 'allowedTypes');
      if ($allowedTypes) {
         if (!is_array($allowedTypes) || count($allowedTypes) === 0) {
            throw new \InvalidArgumentException('El parámetro "allowedTypes" debe ser un array no vacío');
         }
         $this->allowedTypes = $allowedTypes;

         $type = Arr::get($params, 'type', '');
         if (trim($type) !== '' && in_array($type, $allowedTypes)) {
            $this->type = $type;
         } elseif (trim($type) !== '') {
            throw new \InvalidArgumentException('El parámetro "type" debe estar entre los siguientes: ' . implode(', ', $allowedTypes));
         }
      } else {
         $this->type = Arr::get($params, 'type');
      }

      // Options
      $options = Arr::get($params, 'options');
      if ($options) {
         $this->options = $this->prepareOptions($options);
      }
   }

   /**
    * Preparar opciones para componentes tipo select
    */
   protected function prepareOptions($options): Collection
   {
      if ($options instanceof Model || $options instanceof Collection) {
         $options = $options->toArray();
      } elseif (is_object($options)) {
         $options = (array) $options;
      }

      if (!is_array($options)) {
         throw new \InvalidArgumentException(
            'El parámetro "options" debe ser de tipo Model, Collection, array u objeto'
         );
      }

      $response = collect();
      foreach ($options as $option) {
         if (!is_array($option) || !array_key_exists('value', $option) || !array_key_exists('label', $option)) {
            throw new \InvalidArgumentException(
               'Cada opción en "options" debe ser un array con las claves "value" y "label"'
            );
         }
         $response->push([
            'value' => $option['value'],
            'label' => $option['label'],
            'id' => uniqid('option_')
         ]);
      }

      return $response;
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

   public function getValue()
   {
      return $this->value;
   }

   protected function getModel()
   {
      return $this->model;
   }

   protected function setModel($model)
   {
      if ($model) {
         if ($model instanceof Model || $model instanceof Collection) {
            $this->model = $model;
         } elseif (is_array($model) || is_object($model)) {
            $this->model = collect($model);
         } else {
            throw new \InvalidArgumentException(
               'El parámetro "model" debe ser de tipo Model, Collection, array u objeto.'
            );
         }
      }

      return $this->model;
   }

   public function resolveView()
   {
      $view = $this->render();

      if ($view instanceof ViewContract) {
         return $view;
      }

      if ($view instanceof Htmlable) {
         return $view;
      }

      $resolver = function ($view) {
         if ($view instanceof ViewContract) {
            return $view;
         }

         return $this->extractBladeViewFromString($view);
      };

      return $view instanceof Closure ?
         function (array $data = []) use ($view, $resolver) {
            $extractAttrs = json_decode(json_encode($data['attributes']), true);
            $newParams = $this->afterInitialize($extractAttrs);

            foreach($newParams as $key => $newParam){
               if(isset($this[$key])){
                  $this[$key] = $newParam;
                  unset($newParams[$key]);
               }
            }

            $this->extraParams = $newParams;

            return $resolver($view($data));
         } :
         $resolver($view);
    }
}
