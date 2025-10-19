<?php

namespace App\View\Components\Form;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Input extends Component
{
    public $id;
    public $name;
    public $type;
    /**
     * Create a new component instance.
     */
    public function __construct($id = '', $name = '', $type = '')
    {
        if(empty($name)){
            throw new \InvalidArgumentException('El parámetro "name" es requerido');
        }
        if(empty($type)){
            throw new \InvalidArgumentException('El parámetro "type" es requerido');
        }

        $this->id = $id ?? $name;
        $this->name = $name;
        $this->type = $type;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.form.input');
    }
}
