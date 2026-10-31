@php
    $attributes ??= new \Illuminate\View\ComponentAttributeBag();
@endphp

<div class="space-y-2">
    @foreach ($field->getOptions() as $option)
        @include('forms::fields.checkbox', [
            'field' => new \Erag\InertiaForms\Fields\Checkbox($field->getName())->label($option['label'])->live(),
            'value' => $option['value'],
            'statePath' => 'state',
        ])
    @endforeach
</div>
