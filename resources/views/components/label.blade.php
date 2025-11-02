@props(['required' => false, 'id' => '', 'label' => '', 'cssClasses' => ''])

{{-- <div class="label-component-container"> --}}
   <label {{$attributes->class(['form-label'])}} for="{{$id}}">{{$label}}
      @if ($required)
         <span class="required-asterisk">*</span>
      @endif
   </label>
{{-- </div> --}}