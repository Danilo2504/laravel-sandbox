@props(['cssClasses' => ''])

<div @class(['video-container', $cssClasses])>
    <div {{$attributes->class([$getContentClasses])}}>
        {!! $iframe !!}
    </div>
</div>