@php use Xenio\LivewireForms\Fields\Button; @endphp
@props([
    'field',
    'statePath' => 'state',
])

@php
    /**
    * @var Button $field
    */

    $attributes ??= new \Illuminate\View\ComponentAttributeBag();

    $attributes->setAttributes([
        'disabled' => $field->isDisabled(),
        'wire:click.prevent' => $field->getActionMethod(),
        'type' => $field->isSubmit() ? 'submit' : 'button'
    ])
@endphp

<button class="inline-flex justify-center items-center px-4 py-2 text-sm font-medium text-white bg-gamma rounded-md" {{ $attributes }}>
    @if ($field->getProcessingLabel())
        <span wire:loading.remove>{{ $field->getLabel() }}</span>
        <span wire:loading>{{ $field->getProcessingLabel() }}</span>
    @else
        {{ $field->getLabel() }}
    @endif
</button>
