@props(['fieldset', 'statePath' => 'state', 'data' => null])
<section class="custom-fieldset">
    <h2>{{ $fieldset->getLegend() }}</h2>
    @foreach ($fieldset->getFields() as $field)
        @include(config('livewire-forms.components.field', 'forms::field'), [
            'field' => $field,
            'statePath' => $statePath,
            'data' => $data,
        ])
    @endforeach
</section>
