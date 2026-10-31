@php
    $attributes ??= new \Illuminate\View\ComponentAttributeBag();
@endphp

@include('forms::fields.select', [
    'field' => $field,
    'statePath' => $statePath,
    'attributes' => $attributes,
])
