@php use Xenio\LivewireForms\Fields\Callout; @endphp
@props([
    'field',
    'statePath' => 'state',
])

@php
    /**
     * @var Callout $field
     */
@endphp

<div
        @class([
            'flex items-start gap-3 rounded-md border px-4 py-3 text-sm',
            'border-red-800 bg-red-800/10 text-red-700' => $field->getTone() === 'danger',
            'border-gamma bg-gamma/10 text-gamma' => $field->getTone() === 'info' || $field->getTone() === 'success',
        ])
        role="status"
        data-tone="{!! $field->getTone() !!}">
    <svg
            class="mt-[.22em] size-[1em] shrink-0"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            aria-hidden="true">
        {!! $field->getIcon() !!}
    </svg>


    <div class="grid min-w-0 flex-1">
        <p class="font-semibold">
            {!! $field->getTitle() !!}
        </p>
        @if($field->getBody())
            <p class="leading-snug opacity-90">
                {!! $field->getBody() !!}
            </p>
        @endif
    </div>
</div>