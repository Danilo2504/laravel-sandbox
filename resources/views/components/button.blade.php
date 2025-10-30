@props(['format' => 'block', 'url' => '', 'label' => '', 'type' => 'button'])

@if (trim($url) !== '')
   <a 
      href="{{$url}}"
      role="button"
      {{$attributes->class([
         'button-component',
         $formatClasses
      ])}}
   >
      @unless ($label)
         {{$content ?? ''}}
      @else
         {{$icon ?? ''}}
         {!! $label ?? '' !!}
      @endunless
   </a>
@else
   <button
      type="{{$type}}"
      {{$attributes->class([
         'button-component',
         $formatClasses
      ])}}
   >
      @unless ($label)
         {{$content ?? ''}}
      @else
         {{$icon ?? ''}}
         {!! $label ?? '' !!}
      @endunless
   </button>
@endif