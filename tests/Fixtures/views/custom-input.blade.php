@props(['field', 'statePath' => 'state', 'data' => null])
<div class="custom-blade-input">
    <span class="custom-label">{{ $field->getLabel() }}</span>
    <input {!! $field->getWireModel($statePath) !!} class="custom-input-class" />
</div>
