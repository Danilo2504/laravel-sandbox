@props(['type' => 'login'])

<form
    {{$attributes->merge([
        'method' => $transformedMethod,
        'class' => 'form-component'
    ])}}
>
    @csrf
    @if ($needsSimulation)
        @method($method)
    @endif
    @if (!empty($type))
        <input type="hidden" name="operation_type" value="{{$type}}">
    @endif
    {{$slot}}
</form>