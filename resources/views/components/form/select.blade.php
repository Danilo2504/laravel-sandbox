<div @class(['select-component-container', $cssClasses])>
    @unless ($noLabel)
        @empty($customLabel)
            <x-label id="{{$id}}" required="{{$required}}" label="{{$label}}"></x-label>
        @else
            {{$customLabel ?? ''}}
        @endempty
    @endunless
    <select name="{{$name}}" id="{{$id}}" {{$attributes->merge(['class' => 'select-component'])}}>
        <option value="">{{$placeholder}}</option>
        @empty ($customOptions)
            @foreach ($options as $option)
                <option value="{{$option->value}}" @selected($option->value === $value) id="{{$option->id}}">{{$option->label}}</option>
            @endforeach
        @else
            {{$customOptions ?? ''}}
        @endempty
    </select>
</div>