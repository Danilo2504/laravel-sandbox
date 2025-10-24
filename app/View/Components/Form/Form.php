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
    public $model;

    /**
     * Create a new component instance.
     */
    public function __construct($method = "", $model = null)
    {
        if(empty($method)){
            throw new \InvalidArgumentException('El parámetro "method" es requerido');
        }
        
        if($model){
            if(
                $model instanceof \Illuminate\Database\Eloquent\Model ||
                $model instanceof \Illuminate\Support\Collection
            ){
                $this->model = $model;
            } elseif(
                is_array($model) ||
                is_object($model)
            ){
                $this->model = collect($model);
            } else{
                throw new \InvalidArgumentException('El parámetro "model" debe ser de tipo \Illuminate\Database\Eloquent\Model o \Illuminate\Support\Collection o array() o object');
            }
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
