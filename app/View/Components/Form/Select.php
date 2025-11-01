<?php

namespace App\View\Components\Form;

use App\View\Components\Base\BaseComponent;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class Select extends BaseComponent
{
    public $options;
    /**
     * Create a new component instance.
     */
    public function __construct(
        string $name = '',
        string $id = '',
        string $label = '',
        bool $noLabel = false,
        string $placeholder = '- Selecciona -',
        string|array $cssClasses = [],
        string $value = '',
        bool $required = false,
        $model = null,
        $options = [],
    )
    {
        parent::__construct(
            name: $name,
            id:$id,
            label:$label,
            noLabel:$noLabel,
            placeholder:$placeholder,
            cssClasses:$cssClasses,
            value:$value,
            required:$required,
        );

        $this->options = $this->prepareOptions($options);
        $this->setModel($model);
    }

    protected function prepareOptions($options): Collection
    {
        // Normalizar entrada
        if ($options instanceof Model || $options instanceof Collection) {
            $options = $options->toArray();
        } elseif (is_object($options)) {
            $options = (array) $options;
        }

        if (!is_array($options)) {
            throw new InvalidArgumentException(
                'El parámetro "options" debe ser de tipo Model, Collection, array u objeto'
            );
        }

        return collect($options)->map(function ($option) {
            // Validación mínima
            if (!is_array($option) || !isset($option['value'], $option['label'])) {
                throw new InvalidArgumentException(
                    'Cada opción en "options" debe ser un array con las claves "value" y "label"'
                );
            }

            // Convertir a objeto accesible con ->arrow
            return (object) [
                'value' => $option['value'],
                'label' => $option['label'],
                'id'    => $option['id'] ?? uniqid('option_'),
            ];
        });
    }
    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.form.select');
    }
}
