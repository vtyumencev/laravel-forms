@props(['field', 'statePath' => 'state', 'data' => null])
<div class="custom-field-wrapper">
    <label class="custom-wrapper-label">{{ $field->getLabel() }}</label>
    {!! $field->render($statePath, $data) !!}
</div>
