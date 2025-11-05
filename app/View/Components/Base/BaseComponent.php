<?php

namespace App\View\Components\Base;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use Illuminate\View\Component;

/**
 * BaseComponent - Abstract Base Class for Form Components
 *
 * This abstract class provides a foundation for building reusable form components in Laravel.
 * It handles common properties and functionality that most form elements require, such as
 * name, id, label, value, validation states, and CSS classes.
 *
 * Key Features:
 * - Automatic property initialization from Blade component attributes
 * - Model binding support for populating values from Eloquent models or collections
 * - Flexible label handling (with option to hide labels)
 * - CSS class management (supports both string and array formats)
 * - Form element states (required, disabled, readonly)
 * - Automatic ID generation based on name if not provided
 *
 * @package App\View\Components\Base
 * @abstract
 */
abstract class BaseComponent extends Component
{
   /**
    * @var string The name attribute for the form element (required)
    */
   public $name;

   /**
    * @var string The ID attribute for the form element (defaults to name if not provided)
    */
   public $id;

   /**
    * @var string The type attribute (e.g., 'text', 'email', 'password')
    */
   public $type;

   /**
    * @var string The label text for the form element
    */
   public $label;

   /**
    * @var bool Whether to hide the label (default: false)
    */
   public $noLabel;

   /**
    * @var mixed The current value of the form element
    */
   public $value;

   /**
    * @var string Additional CSS classes to apply to the component
    */
   public $cssClasses;

   /**
    * @var bool Whether the field is required (default: false)
    */
   public $required;

   /**
    * @var bool Whether the field is disabled (default: false)
    */
   public $disabled;

   /**
    * @var bool Whether the field is readonly (default: false)
    */
   public $readonly;

   /**
    * @var string Placeholder text for the form element
    */
   public $placeholder;

   /**
    * @var Model|Collection|null The model instance for data binding
    */
   protected $model;

   /**
    * @var string|null The field name for model binding
    */
   protected $field;

   /**
    * BaseComponent Constructor
    *
    * Laravel automatically binds Blade component attributes to these parameters.
    * The constructor collects all parameters and delegates initialization to initializeBaseProperties().
    *
    * @param string $name The name attribute for the form element (required)
    * @param string $id The ID attribute (defaults to name if empty)
    * @param string $type The type attribute for the form element
    * @param string $label The label text (required unless noLabel is true)
    * @param bool $noLabel Whether to hide the label (default: false)
    * @param mixed $value The initial value for the form element
    * @param array|string $cssClasses Additional CSS classes (can be array or string)
    * @param bool $readonly Whether the field is readonly (default: false)
    * @param bool $required Whether the field is required (default: false)
    * @param bool $disabled Whether the field is disabled (default: false)
    * @param string $placeholder Placeholder text for the form element
    *
    * @throws \InvalidArgumentException If name is empty or label is empty when noLabel is false
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
    * Initialize Base Properties
    *
    * This method processes and validates all component parameters, setting appropriate
    * defaults and enforcing business rules:
    * - name is required and cannot be empty
    * - id defaults to name if not provided
    * - label is required unless noLabel is true
    * - cssClasses can be provided as string or array
    *
    * @param array $params Associative array of component parameters
    * @return void
    *
    * @throws \InvalidArgumentException If name is empty
    * @throws \InvalidArgumentException If label is empty when noLabel is false
    * @throws \InvalidArgumentException If cssClasses is neither string nor array
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

   /**
    * Get the name attribute
    *
    * @return string The name attribute value
    */
   public function getName()
   {
      return $this->name;
   }

   /**
    * Get the ID attribute
    *
    * @return string The ID attribute value
    */
   public function getId()
   {
      return $this->id;
   }

   /**
    * Get the type attribute
    *
    * @return string The type attribute value
    */
   public function getType(){
      return $this->type;
   }
   
   /**
    * Get the current value
    *
    * @return mixed The current value of the form element
    */
   public function getValue()
   {
      return $this->value;
   }

   /**
    * Get the label text
    *
    * @return string The label text
    */
   public function getLabel()
   {
      return $this->label;   
   }

   /**
    * Get the bound model or a specific attribute from it
    *
    * This method allows child components to access the bound model or retrieve
    * specific attributes from it. Useful for populating form fields from model data.
    *
    * @param string|null $key Optional attribute key to retrieve from the model
    * @return Model|Collection|mixed|null The model instance, specific attribute, or null
    */
   protected function getModel(?string $key = null)
   {
      if(!$this->model){
         return null;
      }

      return $key ? $this->model->get($key) : $this->model;
   }

   /**
    * Bind a model to the component and auto-populate the value
    *
    * This method accepts various data types and converts them to a Collection for
    * consistent access. If the model contains a property matching the component's
    * name attribute, it automatically populates the component's value.
    *
    * Supported model types:
    * - Eloquent Model: Direct model instance
    * - Collection: Laravel collection
    * - Array: Converted to collection
    * - Object: Converted to collection
    *
    * @param Model|Collection|array|object|null $model The data source to bind
    * @return Collection|null The bound model as a collection
    *
    * @throws \InvalidArgumentException If model is not a supported type
    */
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
               'The "model" parameter must be of type Model, Collection, array, or object.'
            );
         }
      }

      if($this->model->get($this->name)){
         $this->value = $this->model->get($this->name);
      }

      return $this->model;
   }
}
