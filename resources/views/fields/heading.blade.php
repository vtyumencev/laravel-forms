@php use Xenio\LivewireForms\Fields\Heading; @endphp
@props([
    'field',
    'statePath' => 'state',
])

@php
    /**
     * @var Heading $field
     */
@endphp

<h{!! $field->getLevel() !!} class="wp-block-heading mt-theme-sm col-span-full">
    {!! $field->getText() !!}
</h{!! $field->getLevel() !!}>