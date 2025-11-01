@props(['noLabel' => false, 'label' => '', 'required' => false, 'placeholder' => '- Select -', 'cssClasses' => ''])

<div @class(['textarea-component-container', $cssClasses])>
    @unless ($noLabel)
        @empty($customLabel)
            <x-label id="{{$id}}" required="{{$required}}" label="{{$label}}"></x-label>
        @else
            {{$customLabel ?? ''}}
        @endempty
    @endunless
    <textarea
        name="{{$name}}"
        id="{{$id}}"
        @if ($type === 'editor')
            data-editor-html='@json($editorOptions)'
        @endif
        {{$attributes
            ->class(['textarea-component'])
            ->merge([
                'rows' => 5,
                'cols' => 30
            ])
        }}
        :value="$model[$name]"
    ></textarea>
</div>