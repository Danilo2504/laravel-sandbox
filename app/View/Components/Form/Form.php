<?php

namespace App\View\Components\Form;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Form extends Component
{
    private $simulatedMethods = ['PUT', 'PATCH', 'DELETE'];
    public $method;
    public $transformedMethod;

    /**
     * Create a new component instance.
     */
    public function __construct($method = "")
    {
        if(empty($method)){
            throw new \InvalidArgumentException('El parámetro "method" es requerido');
        }

        $this->method = $method;
        $this->transformedMethod = $this->transformMethod($method);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.form.form');
    }

    /**
     * Class Component methods
     */
    public function transformMethod(string $method): string
    {
        return in_array(strtoupper($method), $this->simulatedMethods) ? 'POST' : strtoupper($method);
    }

    public function needsSimulation(): bool
    {
        return in_array(strtoupper($this->method), $this->simulatedMethods);
    }
}
