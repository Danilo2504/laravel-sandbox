<?php

namespace App\View\Components\Form;

use App\View\Components\Base\BaseComponent;
use Closure;
use Illuminate\Contracts\View\View;

class Textarea extends BaseComponent
{
    public $allowedTypes;
    /**
     * Inicialización personalizada para Textarea
     */
    public function __construct(
        string $name = '',
        string $id = '',
        string $label = '',
        bool $noLabel = false,
        string $type = 'text',
        string|array $cssClasses = [],
        string $value = '',
        bool $required = false,
    )
    {
        parent::__construct(
            name: $name,
            id:$id,
            label:$label,
            type:$type,
            noLabel:$noLabel,
            cssClasses:$cssClasses,
            value:$value,
            required:$required
        );

        $this->allowedTypes = ['text', 'editor'];

        if (!in_array($this->type, $this->allowedTypes)) {
            throw new \InvalidArgumentException('El parámetro "type" debe estar entre los siguientes: ' . implode(', ', $this->allowedTypes));
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.form.textarea');
    }
}
