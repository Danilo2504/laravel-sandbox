@props(['noLabel' => false, 'label' => '', 'required' => false, 'placeholder' => '- Select -', 'cssClasses' => ''])

<div @class(['textarea-component-container', $cssClasses])>
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
    <textarea name="{{$name}}" id="{{$id}}" {{$attributes->class(['textarea-component'])->merge(['rows' => 5, 'cols' => 30])}} :value="$model[$name]"></textarea>
</div>