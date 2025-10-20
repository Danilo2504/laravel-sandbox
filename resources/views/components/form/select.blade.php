@props(['placeholder' => '- Select -', 'cssClasses' => '', 'value' => ''])

<select name="{{$name}}" id="{{$id}}" {{$attributes->merge(['class' => 'select-component'])}}>
    <option @class([$cssClasses]) value="" @selected($option->value === $value)>{{$placeholder}}</option>
    @unless (empty($customOptions))
        @foreach ($options as $option)
            <option @class([$cssClasses]) value="{{$option->value}}" @selected($option->value === $value) id="{{$option->id}}">{{$option->label}}</option>
        @endforeach
    @else
        {{$customOptions ?? ''}}
    @endunless
</select>