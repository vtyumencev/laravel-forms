@props([
    'field',
    'statePath' => 'state',
])

@php
    $attributes ??= new \Illuminate\View\ComponentAttributeBag();
    $variantClass = match($field->getVariant()) {
        'secondary' => 'bg-gray-100 text-gray-900 hover:bg-gray-200 dark:bg-zinc-700 dark:text-white dark:hover:bg-zinc-600',
        'danger' => 'bg-red-600 text-white hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-800',
        'outline' => 'border border-gray-300 bg-transparent text-gray-700 hover:bg-gray-50 dark:border-zinc-600 dark:text-gray-200 dark:hover:bg-zinc-800',
        'ghost' => 'bg-transparent text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-zinc-800',
        'link' => 'bg-transparent text-indigo-600 underline hover:text-indigo-800 dark:text-indigo-400',
        default => 'bg-indigo-600 text-white hover:bg-indigo-700',
    };
    $sizeClass = match($field->getSize()) {
        'sm' => 'px-3 py-1.5 text-xs',
        'lg' => 'px-6 py-3 text-base',
        default => 'px-4 py-2 text-sm',
    };
    $widthClass = $field->isFullWidth() ? 'w-full' : '';
@endphp

<button
    type="submit"
    @if ($field->isDisabled()) disabled @endif
    {{ $attributes->merge(['class' => "inline-flex justify-center items-center font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 {$variantClass} {$sizeClass} {$widthClass}"]) }}
>
    @if ($field->getProcessingLabel())
        <span wire:loading.remove>{{ $field->getLabel() }}</span>
        <span wire:loading>{{ $field->getProcessingLabel() }}</span>
    @else
        {{ $field->getLabel() }}
    @endif
</button>
