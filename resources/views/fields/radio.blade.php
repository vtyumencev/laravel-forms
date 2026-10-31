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

    $attributes->setAttributes([
        'type' => 'radio',
        'disabled' => $field->isDisabled()
    ]);
@endphp

<div @class([
        'flex flex-wrap gap-4 gap-y-2' => $field->isInline(),
        'flex flex-col gap-y-2' => !$field->isInline() && !$field->isButtons(),
        'flex flex-wrap inline-flex' => $field->isButtons(),
        'grid gap-4' => $field->getColumns(),
        'md:grid-cols-2' => $field->getColumns() === 2,
        'grid-cols-3' => $field->getColumns() === 3,
        'grid-cols-2 md:grid-cols-4' => $field->getColumns() === 4,
    ])>
    @foreach ($options as $option)
        @php
            $val = is_array($option) ? $option['value'] : $option;
            $lbl = is_array($option) ? $option['label'] : $option;
            $description = is_array($option) ? $option['description'] : "";
        @endphp
        <label @class([
                'flex',
                'border border-gamma not-last:-mr-px px-4 py-1 bg-gamma/10 has-checked:bg-gamma has-checked:text-omega has-focus-visible:ring-2 has-focus-visible:ring-psi duration-300 cursor-pointer' => $field->isButtons(),
                'rounded-s-sm' => $field->isButtons() && $loop->first,
                'rounded-e-sm' => $field->isButtons() && $loop->last,
                'flex cursor-pointer duration-300 items-start rounded-lg border border-gamma/50 p-3 has-checked:border-gamma has-checked:bg-gamma/10 has-disabled:cursor-not-allowed has-disabled:opacity-60' => $field->getColumns(),
            ])>
            <input
                name="{{ $wireModel }}"
                {{ $binding }}="{{ $wireModel }}"
                value="{{ $val }}"
                {{ $attributes->class([
                    'mr-[0.6em] shrink-0 size-[0.8em] mt-[0.15em] border-gray-300 text-indigo-600 focus:ring-indigo-500 accent-gamma' => !$field->isButtons(),
                    'sr-only' => $field->isButtons()
                ]) }}
            />
            <span class="block">
                <span class="block">
                    {{ $lbl }}
                </span>
                @if ($description)
                    <span class="block text-xs">
                        {{ $description }}
                    </span>
                @endif
            </span>
        </label>
    @endforeach
</div>
