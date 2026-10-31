@props([
    'field',
    'statePath' => 'state',
])

@php
    $attributes ??= new \Illuminate\View\ComponentAttributeBag();
    $wireModel = $field->wireModelName($statePath);
    $modifier = $field->getLivewireModifier();
    $binding = 'wire:model' . ($modifier ? '.' . $modifier : '');
@endphp

<textarea
    id="{{ $wireModel }}"
    {{ $binding }}="{{ $wireModel }}"
    rows="{{ $field->getRows() }}"
    @if ($field->getPlaceholder()) placeholder="{{ $field->getPlaceholder() }}" @endif
    @if ($field->isDisabled()) disabled @endif
    @if ($field->isReadonly()) readonly @endif
    @if ($field->isAutofocus()) autofocus @endif
    {{ $attributes->merge(['class' => 'block w-full px-[.6em] py-[.5em] border border-gamma/50 field-sizing-content min-h-[4lh] disabled:opacity-50']) }}
></textarea>
