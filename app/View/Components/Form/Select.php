<?php

namespace App\View\Components\Form;

use App\View\Components\Base\BaseComponent;
use Closure;
use Illuminate\Contracts\View\View;

class Select extends BaseComponent
{
    /**
     * Create a new component instance.
     */

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.form.select');
    }
}
