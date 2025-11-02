@props(['format' => 'block', 'url' => '', 'label' => '', 'type' => 'button'])

@if (trim($url) !== '')
   <a 
      href="{{$url}}"
      role="button"
      {{$attributes->class([
         'btn btn-link',
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
         'btn btn-primary',
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