<?php

namespace App\View\Components\Form;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Select extends Component
{
    public $id;
    public $name;
    public $options;

    /**
     * Create a new component instance.
     */
    public function __construct($id = '', $name = '', $options = [])
    {
        if(empty($name)){
            throw new \InvalidArgumentException('El parámetro "name" es requerido');
        }
        if(!is_array($options) || $options instanceof \Illuminate\Support\Collection){
            throw new \InvalidArgumentException('El parámetro "options" deber ser un array o una instancia de \Illuminate\Support\Collection');
        }

        $this->options = collect();

        foreach($options as $option){
            if(!array_key_exists('value', $option) || !array_key_exists('label', $option)){
                throw new \InvalidArgumentException('Cada opción en "options" debe tener las claves "value" y "label"');
            }
            $this->options->push(['value' => $option['value'], 'label' => $option['label'], 'id' => uniqid('option_')]);
        }

        $this->id = empty($id) ? $name : $id;
        $this->name = $name;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.form.select');
    }
}
