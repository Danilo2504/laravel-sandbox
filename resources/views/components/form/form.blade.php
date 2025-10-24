@props(['operationType' => ''])

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
    @if (!empty($operationType))
        <input type="hidden" name="operation_type" value="{{$operationType}}">
    @endif
    {{$alerts ?? ''}}
    {{$slot}}
    {{$actions ?? ''}}
</form>