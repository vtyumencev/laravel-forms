@php
    use Erag\InertiaForms\Fields\Button;
    use Erag\InertiaForms\Fields\Checkbox;
    use Erag\InertiaForms\Fields\Hidden;
    use Erag\InertiaForms\Fields\Radio;
    use Erag\InertiaForms\Fields\Submit;
    use Illuminate\View\ComponentAttributeBag;
@endphp

@props([
    'field',
    'statePath' => 'state',
    'data' => null,
])

@php
    $state = $data ?? ((isset($this) && isset($this->{$statePath}) && is_array($this->{$statePath})) ? $this->{$statePath} : []);
@endphp

@if ($field->isAuthorized() && $field->isVisibleFor($state))
    @php
        $attributes ??= new ComponentAttributeBag();
        $wireModel = $field->wireModelName($statePath);
        $spanClass = match($field->getColumnSpan()) {
            2 => 'md:col-span-2',
            3 => 'md:col-span-3',
            4 => 'md:col-span-4',
            default => '',
        };
    @endphp

    <div wire:key="field-wrapper-{{ $wireModel }}" class="w-full {{ $spanClass }} {{ $field->getClass() ?? '' }}">
        @if ($field instanceof Radio)
            <span class="block text-sm font-bold mb-1">
                {{ $field->getLabel() }}
            </span>
        @elseif (! $field instanceof Button && ! $field instanceof Hidden && ! $field instanceof Checkbox)
            <label for="{{ $wireModel }}" class="block text-sm font-bold mb-1">
                {{ $field->getLabel() }}
                @if ($field->isRequired())
                    <span class="text-red-500">*</span>
                @endif
            </label>
        @endif

        {!! $field->render($statePath, $state) !!}

        @if ($field->getHelp())
            <p class="mt-1 text-xs text-gray-500">{{ $field->getHelp() }}</p>
        @endif

        @if (isset($errors) && $errors->has($wireModel))
            <p class="mt-1 text-sm text-red-600">{{ $errors->first($wireModel) }}</p>
        @endif
    </div>
@endif
