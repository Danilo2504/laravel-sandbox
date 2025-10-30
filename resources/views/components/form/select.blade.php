@props(['noLabel' => false, 'label' => '', 'value' => '', 'required' => false, 'placeholder' => '- Select -', 'cssClasses' => ''])

<div @class(['select-component-container', $cssClasses])>
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
    @endunless
    <select name="{{$name}}" id="{{$id}}" {{$attributes->merge(['class' => 'select-component'])}}>
        <option value="">{{$placeholder}}</option>
        @empty ($customOptions)
            @foreach ($options as $option)
                <option value="{{$option['value']}}" @selected($option['value'] === $value) id="{{$option['id']}}">{{$option['label']}}</option>
            @endforeach
        @else
            {{$customOptions ?? ''}}
        @endempty
    </select>
</div>