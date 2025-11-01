<div @class(['input-component-container', $cssClasses])>
    @unless ($noLabel)
        @empty($customLabel)
            <x-label id="{{$id}}" required="{{$required}}" label="{{$label}}"></x-label>
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
                        'value' => $value ?? '',
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
                    'value' => $value ?? ''
                ])}}
            >
            @break
        @default
            <input {{$attributes->merge([
                'class'=> 'input-component',
                'type' => $type,
                'name' => $name,
                'id' => $id,
                'value' => $value ?? ''
            ])}}>
    @endswitch
    {{$helperText ?? ''}}
</div>