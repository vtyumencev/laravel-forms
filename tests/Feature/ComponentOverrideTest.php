<?php

use Erag\InertiaForms\Fields\Fieldset;
use Erag\InertiaForms\Fields\Textarea;
use Erag\InertiaForms\Fields\TextInput;
use Erag\InertiaForms\Form;
use Illuminate\Support\Facades\View;

beforeEach(function () {
    View::addNamespace('test-fixtures', __DIR__.'/../Fixtures/views');
});

it('resolves default package views for fields when unconfigured', function () {
    $text = TextInput::make('first_name');
    $textarea = Textarea::make('bio');

    expect($text->getView())->toBe('forms::fields.text-input')
        ->and($textarea->getView())->toBe('forms::fields.textarea');
});

it('resolves field views by kebab-case config mapping', function () {
    config()->set('inertia-forms.components.text-input', 'custom.kebab.input');

    $field = TextInput::make('first_name');

    expect($field->getView())->toBe('custom.kebab.input');
});

it('resolves field views by component name config mapping', function () {
    config()->set('inertia-forms.components.TextInput', 'custom.pascal.input');

    $field = TextInput::make('first_name');

    expect($field->getView())->toBe('custom.pascal.input');
});

it('resolves field views by class FQCN config mapping', function () {
    config()->set('inertia-forms.components.'.TextInput::class, 'custom.fqcn.input');

    $field = TextInput::make('first_name');

    expect($field->getView())->toBe('custom.fqcn.input');
});

it('prioritizes class FQCN over component name and kebab name in config', function () {
    config()->set('inertia-forms.components.text-input', 'custom.kebab.input');
    config()->set('inertia-forms.components.TextInput', 'custom.pascal.input');
    config()->set('inertia-forms.components.'.TextInput::class, 'custom.fqcn.input');

    $field = TextInput::make('first_name');

    expect($field->getView())->toBe('custom.fqcn.input');
});

it('prioritizes component name over kebab name in config', function () {
    config()->set('inertia-forms.components.text-input', 'custom.kebab.input');
    config()->set('inertia-forms.components.TextInput', 'custom.pascal.input');

    $field = TextInput::make('first_name');

    expect($field->getView())->toBe('custom.pascal.input');
});

it('prioritizes instance-level view override over config mappings', function () {
    config()->set('inertia-forms.components.'.TextInput::class, 'custom.fqcn.input');

    $field = TextInput::make('first_name')->view('custom.instance.input');

    expect($field->getView())->toBe('custom.instance.input');
});

it('resolves fieldset and form views by default, config, and instance overrides', function () {
    $fieldset = Fieldset::make('Personal Info');
    $form = new class extends Form
    {
        public function fields(): array
        {
            return [];
        }
    };

    expect($fieldset->getView())->toBe('forms::fieldset')
        ->and($form->getView())->toBe('forms::form');

    // Config mapping by string
    config()->set('inertia-forms.components.fieldset', 'custom.fieldset');
    config()->set('inertia-forms.components.form', 'custom.form');

    expect($fieldset->getView())->toBe('custom.fieldset')
        ->and($form->getView())->toBe('custom.form');

    // Config mapping by FQCN
    config()->set('inertia-forms.components.'.Fieldset::class, 'custom.fqcn.fieldset');
    expect($fieldset->getView())->toBe('custom.fqcn.fieldset');

    // Instance-level override
    $fieldset->view('custom.instance.fieldset');
    $form->view('custom.instance.form');

    expect($fieldset->getView())->toBe('custom.instance.fieldset')
        ->and($form->getView())->toBe('custom.instance.form');
});

it('renders custom Blade components configured via inertia-forms.components', function () {
    config()->set('inertia-forms.components.text-input', 'test-fixtures::custom-input');
    config()->set('inertia-forms.components.field', 'test-fixtures::custom-wrapper');
    config()->set('inertia-forms.components.fieldset', 'test-fixtures::custom-fieldset');
    config()->set('inertia-forms.components.form', 'test-fixtures::custom-form');

    $form = new class extends Form
    {
        public function fields(): array
        {
            return [
                Fieldset::make('User Details')->fields([
                    TextInput::make('email')->label('Email Address')->live(),
                ]),
            ];
        }
    };

    $html = $form->render();

    expect($html)->toContain('custom-form-container')
        ->and($html)->toContain('custom-fieldset')
        ->and($html)->toContain('custom-field-wrapper')
        ->and($html)->toContain('custom-wrapper-label')
        ->and($html)->toContain('custom-blade-input')
        ->and($html)->toContain('custom-input-class')
        ->and($html)->toContain('wire:model.live="state.email"');
});

it('renders instance-level custom view without touching global config', function () {
    $field = TextInput::make('custom_field')
        ->label('Custom Field')
        ->view('test-fixtures::custom-input');

    $html = $field->render('state');

    expect($html)->toContain('custom-blade-input')
        ->and($html)->toContain('custom-input-class')
        ->and($html)->toContain('wire:model="state.custom_field"');
});
