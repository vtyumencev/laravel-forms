@props([
    'field',
    'statePath' => 'state'
])

@php
    $wireModel = $field->wireModelName($statePath);
    $hasError = isset($errors) && $errors->has($wireModel);
@endphp

<input
    {{ $field->getInputAttributes($statePath)->class([
        'block w-full border px-[.6em] py-[.5em] transition-colors duration-200 disabled:bg-slate-100 disabled:opacity-60',
        'border-red-300' => $hasError,
        'border-gamma/50 bg-white text-slate-900' => !$hasError,
    ]) }}
/>
