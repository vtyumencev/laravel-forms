@props([
    'field',
    'statePath' => 'state',
])

@php
    $attributes ??= new \Illuminate\View\ComponentAttributeBag();
    $wireModel = $field->wireModelName($statePath);
    $modifier = $field->getLivewireModifier();
    $binding = 'wire:model' . ($modifier ? '.' . $modifier : '');
    $options = $field->getOptions();
@endphp

<select
    id="{{ $wireModel }}"
    {{ $binding }}="{{ $wireModel }}"
    @if ($field->isDisabled()) disabled @endif
    @if ($field->isAutofocus()) autofocus @endif
    {{ $attributes->merge(['class' => 'block w-full px-[.6em] py-[.5em] border border-gamma/50 disabled:opacity-50']) }}
>
    @foreach ($options as $option)
        @if (is_array($option) && array_key_exists('value', $option))
            <option value="{{ $option['value'] }}" @if(!empty($option['disabled'])) disabled @endif>
                {{ $option['label'] }}
            </option>
        @else
            <option value="{{ $option }}">{{ $option }}</option>
        @endif
    @endforeach
</select>
