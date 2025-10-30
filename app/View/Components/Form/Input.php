<?php

namespace App\View\Components\Form;

use App\View\Components\Base\BaseComponent;
use Closure;
use Illuminate\Contracts\View\View;

class Input extends BaseComponent
{
    /**
     * Inicialización personalizada para Input
     */
    protected function afterInitialize(array $params): array
    {
        return ['allowedTypes' => ['text', 'email', 'password', 'number', 'hidden', 'tel']];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.form.input');
    }
}
