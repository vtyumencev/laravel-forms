@php
    use Xenio\LivewireForms\Fields\Button;
    use Xenio\LivewireForms\Fields\Callout;
    use Xenio\LivewireForms\Fields\Checkbox;
    use Xenio\LivewireForms\Fields\Heading;
    use Xenio\LivewireForms\Fields\Hidden;
    use Xenio\LivewireForms\Fields\Radio;
    use Xenio\LivewireForms\Fields\Submit;
    use Xenio\LivewireForms\Fields\DisplayField;
    use Xenio\LivewireForms\Fields\Summary;
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
    @endphp

    @if(! $field instanceof Heading && ! $field instanceof Summary)

    <div @class([
            'w-full',
            $field->getClass(),
            'md:col-span-2' => $field->getColumnSpan() === 2,
            'md:col-span-3' => $field->getColumnSpan() === 3,
            'md:col-span-4' => $field->getColumnSpan() === 4,
            ])
         wire:key="field-wrapper-{{ $wireModel }}">
        @if ($field instanceof Radio)
            <span class="block text-sm font-bold mb-1">
                {!! $field->getLabel() !!}
            </span>
        @elseif (! $field instanceof Button && ! $field instanceof Hidden && ! $field instanceof Checkbox && ! $field instanceof DisplayField)
            <label for="{{ $wireModel }}" class="block text-sm font-bold mb-1">
                {{ $field->getLabel() }}
                @if ($field->isRequired())
                    <span class="text-red-500">*</span>
                @endif
            </label>
        @endif

    @endif

        {!! $field->render($statePath, $state) !!}

        @if ($field->getHelp())
            <p class="mt-1 text-xs text-gray-500">{{ $field->getHelp() }}</p>
        @endif

    @if(! $field instanceof Heading && ! $field instanceof Summary)
        @if (isset($errors) && $errors->has($wireModel))
            <p class="mt-1 text-sm text-red-600">
                {!! $errors->first($wireModel) !!}
            </p>
        @endif
    </div>

    @endif
@endif
