@props([
    'field',
    'statePath' => 'state',
    'value' => null
])

@php
    $attributes ??= new \Illuminate\View\ComponentAttributeBag();
    $wireModel = $field->wireModelName($statePath);
    $modifier = $field->getLivewireModifier();
    $binding = 'wire:model' . ($modifier ? '.' . $modifier : '');

    $v = $value ?
        "\${\$wire." . $wireModel . ".includes('" . $value . "') ? 0 : 1}" :
        "\${\$wire." . $wireModel . " ? 0 : 1}";

    $attributes->setAttributes([
        'type' => 'checkbox',
        $binding => $wireModel,
        'disabled' => $field->isDisabled(),
        'value' => $value
    ]);

@endphp

<label class="flex">
    <span class="shrink-0 mt-[0.01em]">
        <span class="relative flex items-center justify-center">
            <input {{ $attributes->class('sr-only peer') }} />
            <span class="size-[1.1em] border border-gamma peer-focus:ring peer-focus:ring-indigo-500"></span>
            <span x-cloak class="cursor-pointer absolute size-[110%]">
                <svg
                    class="size-full"
                    viewBox="0 -0.5 25 25"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                    aria-hidden="true">
                    <path
                        d="M5.5 12.5L10.167 17L19.5 8"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        pathLength="1"
                        class="transition-[stroke-dashoffset] duration-300 ease-out"
                        :style="`stroke-dasharray: 1; stroke-dashoffset: {!! $v !!}`"
                    />
                </svg>
            </span>
        </span>
    </span>
    <span class="ml-[0.7em] block">
        {!! $field->getLabel() !!}
        @if ($field->isRequired())
            <span class="text-red-500">*</span>
        @endif
    </span>
</label>
