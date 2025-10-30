<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Button extends Component
{
    public $formatClasses;
    /**
     * Create a new component instance.
     */
    public function __construct($format = 'inline')
    {
        if(empty($format)){
            throw new \InvalidArgumentException('El parámetro "format" es requerido');
        }

        $this->formatClasses = match(trim($format)) {
            'inline' => 'button-inline',
            'block' => 'button-block',
            'clean' => 'button-clean',
        };
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.button');
    }
}
