@props(['required' => false, 'id' => '', 'label' => ''])

<div class="label-component-container">
   <label class="label-component" for="{{$id}}">{{$label}}
      @if ($required)
         <span class="required-asterisk">*</span>
      @endif
   </label>
</div>