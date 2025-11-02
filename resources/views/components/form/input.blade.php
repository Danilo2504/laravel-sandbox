<div @class(['form-group', $cssClasses])>
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
                        'class' => 'form-control',
                        'name' => $name,
                        'id' => $id,
                        'value' => $value ?? '',
                    ])}}
                >
                <i class="fas fa-eye toggle-password" data-toggle="#{{$id}}"></i>
            </div>
            @break
        @case('custom')
            {{$input ?? ''}}
            @break
        @default
            <input {{$attributes->merge([
                'class'=> 'form-control',
                'type' => $type,
                'name' => $name,
                'id' => $id,
                'value' => $value ?? ''
            ])}}>
    @endswitch
    {{$helperText ?? ''}}
</div>