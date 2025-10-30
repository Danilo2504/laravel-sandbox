<?php

namespace App\View\Components\Form;

use App\View\Components\Base\BaseComponent;
use Closure;
use Illuminate\Contracts\View\View;

class Textarea extends BaseComponent
{
    // /**
    //  * Inicialización personalizada para Textarea
    //  */
    // protected function initialize(array $params): void
    // {
    //     // Definir tipos permitidos para textarea
    //     $this->allowedTypes = ['text', 'editor'];
        
    //     // Validar type si está presente
    //     if ($this->type && !in_array($this->type, $this->allowedTypes)) {
    //         throw new \InvalidArgumentException(
    //             'El parámetro "type" debe ser uno de los siguientes valores: ' . implode(', ', $this->allowedTypes)
    //         );
    //     }
        
    //     // Default type
    //     if (!$this->type) {
    //         $this->type = 'text';
    //     }
    // }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.form.textarea');
    }
}
