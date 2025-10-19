@props(['noLabel' => false, 'label' => '', 'value' => '', 'cssClasses' => '', 'required' => false])

<div @class(['input-component-container', $cssClasses])>
    @unless ($noLabel)
        @empty($customLabel)
            <label class="label-component" for="{{$id}}">{{$label}}</label>

            @if ($required)
                <span class="required-asterisk">*</span>
            @endif
        @else
            {{$customLabel ?? ''}}
        @endempty
        {{-- errors for Beers --}}
    @endunless
    @switch($type)
        @case('password')
            <div class="password-component-container">
                <input
                    type="password"
                    {{$attributes->merge([
                        'class' => 'password-component',
                        'name' => $name,
                        'id' => $id,
                        'value' => $value,
                    ])}}
                >
                <i class="fas fa-eye toggle-password" data-toggle="#{{$id}}"></i>

                {{-- Agregar campos de "Recordar contrasena" y "Olvidaste tu contrasena" --}}
            </div>
            @break
        @case('checkbox')
            {{-- Checkbox --}}
            @break
        @case('textarea')
            <textarea {{$attributes->merge([
                'class' => 'textarea-component',
                'name' => $name,
                'id' => $id,
                'value' => $value,
                'rows' => 4
            ])}}></textarea>
            @break
        @case('hidden')
            <input
                type="hidden"
                {{$attributes->merge([
                    'name' => $name,
                    'id' => $id,
                    'value' => $value
                ])}}
            >
        @default
            <input {{$attributes->merge([
                'class'=> 'input-component',
                'type' => $type,
                'name' => $name,
                'id' => ($id),
                'value' => ($value)
            ])}}>
    @endswitch
    {{$helperText}}
</div>