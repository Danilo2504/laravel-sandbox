@aware(['model'])
@props(['noLabel' => false, 'label' => '', 'value' => '', 'cssClasses' => '', 'required' => false])

<div @class(['input-component-container', $cssClasses])>
    @unless ($noLabel)
        @empty($customLabel)
            <label class="label-component" for="{{$id}}">{{$label}}
                @if ($required)
                    <span class="required-asterisk">*</span>
                @endif
            </label>
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
                        'value' => $model[$name] ?? $value ?? '',
                    ])}}
                >
                <i class="fas fa-eye toggle-password" data-toggle="#{{$id}}"></i>
            </div>
            @break
        @case('hidden')
            <input
                type="hidden"
                {{$attributes->merge([
                    'name' => $name,
                    'id' => $id,
                    'value' => $model[$name] ?? $value ?? ''
                ])}}
            >
            @break
        @default
            <input {{$attributes->merge([
                'class'=> 'input-component',
                'type' => $type,
                'name' => $name,
                'id' => $id,
                'value' => $model[$name] ?? $value ?? ''
            ])}}>
    @endswitch
    {{$helperText ?? ''}}
</div>