@props([
    'fieldset',
    'statePath' => 'state',
    'data' => null,
])

@php
    $state = $data ?? ((isset($this) && isset($this->{$statePath}) && is_array($this->{$statePath})) ? $this->{$statePath} : []);
    $columns = $fieldset->getColumns();
    $gridCols = match($columns) {
        2 => 'grid-cols-1 md:grid-cols-2',
        3 => 'grid-cols-1 md:grid-cols-3',
        4 => 'grid-cols-1 md:grid-cols-4',
        default => 'grid-cols-1',
    };
@endphp

@if ($fieldset->isAuthorized() && $fieldset->isVisibleFor($state))
<fieldset class="space-y-4 {{ $fieldset->getClass() ?? '' }}">
    @if ($fieldset->getLegend())
        <legend class="text-base font-semibold text-gray-900">{{ $fieldset->getLegend() }}</legend>
    @endif

    @if ($fieldset->getDescription())
        <p class="text-sm text-gray-500">{{ $fieldset->getDescription() }}</p>
    @endif

    <div class="grid {{ $gridCols }} gap-4">
        @foreach ($fieldset->getFields() as $field)
            @if ($field->isAuthorized() && $field->isVisibleFor($state))
                @include(config('inertia-forms.components.field', 'forms::field'), [
                    'field' => $field,
                    'statePath' => $statePath,
                    'data' => $state,
                ])
            @endif
        @endforeach
    </div>
</fieldset>
@endif
