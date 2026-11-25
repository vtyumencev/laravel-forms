@php use Xenio\LivewireForms\Fields\Submit; @endphp
@props([
    'field',
    'statePath' => 'state',
])

@php
    /**
    * @var Submit $field
    */
@endphp

@include(config('livewire-forms.components.button', 'forms::fields.button'), [
    'field' => $field->submit(),
    'statePath' => $statePath,
])