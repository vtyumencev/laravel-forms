@props(['form', 'statePath' => 'state', 'action' => null, 'currentStep' => null, 'data' => null])
<form class="custom-form-container" wire:submit.prevent="{{ $action ?? 'save' }}">
    @foreach ($form->getFieldsets() as $fieldset)
        @include($fieldset->getView(), [
            'fieldset' => $fieldset,
            'statePath' => $statePath,
            'data' => $data,
        ])
    @endforeach
</form>
