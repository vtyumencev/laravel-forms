<?php

use Xenio\LivewireForms\Concerns\InteractsWithForms;
use Xenio\LivewireForms\Fields\Checkbox;
use Xenio\LivewireForms\Fields\Fieldset;
use Xenio\LivewireForms\Fields\Radio;
use Xenio\LivewireForms\Fields\Select;
use Xenio\LivewireForms\Fields\Submit;
use Xenio\LivewireForms\Fields\Textarea;
use Xenio\LivewireForms\Fields\TextInput;
use Xenio\LivewireForms\Form;
use Illuminate\Validation\ValidationException;

class SampleContactForm extends Form
{
    public function fields(): array
    {
        return [
            Fieldset::make('Personal Details')->fields([
                TextInput::make('name')->required()->placeholder('Your full name'),
                TextInput::make('email')->email()->required()->live(),
                TextInput::make('search_query')->debounce(300),
            ]),
            Fieldset::make('Preferences')->fields([
                Textarea::make('bio')->rows(5)->placeholder('Tell us about yourself'),
                Select::make('country')->options([
                    'de' => 'Germany',
                    'us' => 'United States',
                ])->required(),
                Checkbox::make('terms')->required(),
                Radio::make('newsletter_type')->options([
                    'daily' => 'Daily Digest',
                    'weekly' => 'Weekly Roundup',
                ]),
                Submit::make('Save Changes')->processingLabel('Saving...'),
            ]),
        ];
    }
}

it('renders text input with Livewire wire:model binding and modifiers', function () {
    $field = TextInput::make('username')->placeholder('Enter username');
    expect($field->getWireModel('state'))->toBe('wire:model="state.username"');

    $html = $field->render('state');
    expect($html)->toContain('wire:model="state.username"')
        ->and($html)->toContain('type="text"')
        ->and($html)->toContain('placeholder="Enter username"');

    $field->live();
    expect($field->getWireModel('state'))->toBe('wire:model.live="state.username"')
        ->and($field->render('state'))->toContain('wire:model.live="state.username"');

    $field->debounce(500);
    expect($field->getWireModel('state'))->toBe('wire:model.live.debounce.500ms="state.username"')
        ->and($field->render('state'))->toContain('wire:model.live.debounce.500ms="state.username"');

    $field->blur();
    expect($field->getWireModel('state'))->toBe('wire:model.live.blur="state.username"')
        ->and($field->render('state'))->toContain('wire:model.live.blur="state.username"');
});

it('renders textarea with Livewire binding', function () {
    $field = Textarea::make('message')->rows(4)->placeholder('Type your message');
    $html = $field->render('state');

    expect($html)->toContain('wire:model="state.message"')
        ->and($html)->toContain('rows="4"')
        ->and($html)->toContain('placeholder="Type your message"');
});

it('renders select with options and Livewire binding', function () {
    $field = Select::make('role')->options([
        'admin' => 'Administrator',
        'editor' => 'Editor',
    ]);
    $html = $field->render('state');

    expect($html)->toContain('wire:model="state.role"')
        ->and($html)->toContain('value="admin"')
        ->and($html)->toContain('Administrator')
        ->and($html)->toContain('value="editor"')
        ->and($html)->toContain('Editor');
});

it('renders checkbox and radio fields with Livewire binding', function () {
    $checkbox = Checkbox::make('agree')->label('I agree to the terms');
    $checkboxHtml = $checkbox->render('state');

    expect($checkboxHtml)->toContain('type="checkbox"')
        ->and($checkboxHtml)->toContain('wire:model="state.agree"')
        ->and($checkboxHtml)->toContain('I agree to the terms');

    $radio = Radio::make('gender')->options(['m' => 'Male', 'f' => 'Female']);
    $radioHtml = $radio->render('state');

    expect($radioHtml)->toContain('type="radio"')
        ->and($radioHtml)->toContain('name="state.gender"')
        ->and($radioHtml)->toContain('wire:model="state.gender"')
        ->and($radioHtml)->toContain('Male')
        ->and($radioHtml)->toContain('Female');
});

it('renders submit button with processing label', function () {
    $submit = Submit::make('Submit Form')->processingLabel('Please wait...');
    $html = $submit->render('state');

    expect($html)->toContain('type="submit"')
        ->and($html)->toContain('Submit Form')
        ->and($html)->toContain('Please wait...')
        ->and($html)->toContain('wire:loading');
});

it('renders full form and fieldsets to HTML', function () {
    $form = SampleContactForm::make();
    $html = $form->render('state', 'save');

    expect($html)->toContain('wire:submit.prevent="save"')
        ->and($html)->toContain('Personal Details')
        ->and($html)->toContain('Preferences')
        ->and($html)->toContain('wire:model="state.name"')
        ->and($html)->toContain('wire:model.live="state.email"')
        ->and($html)->toContain('wire:model.live.debounce.300ms="state.search_query"')
        ->and($html)->toContain('wire:model="state.bio"')
        ->and($html)->toContain('wire:model="state.country"')
        ->and($html)->toContain('wire:model="state.terms"')
        ->and($html)->toContain('Save Changes');

    expect((string) $form)->toBe($form->toHtml());
});

it('provides prefixed validation rules for Livewire state', function () {
    $form = SampleContactForm::make();
    $rules = $form->validationRules('state');

    expect($rules)->toHaveKey('state.name')
        ->and($rules)->toHaveKey('state.email')
        ->and($rules)->toHaveKey('state.country')
        ->and($rules)->toHaveKey('state.terms')
        ->and($rules['state.name'])->toContain('required')
        ->and($rules['state.email'])->toContain('email');
});

it('validates state data using form validateData helper', function () {
    $form = SampleContactForm::make();

    $validData = [
        'state' => [
            'name' => 'Alice Doe',
            'email' => 'alice@example.com',
            'search_query' => '',
            'bio' => 'Developer',
            'country' => 'de',
            'terms' => true,
            'newsletter_type' => 'weekly',
        ],
    ];

    $validated = $form->validateData($validData, 'state');
    expect($validated['name'])->toBe('Alice Doe')
        ->and($validated['email'])->toBe('alice@example.com');

    expect(fn () => $form->validateData(['state' => ['name' => '', 'email' => 'invalid']], 'state'))
        ->toThrow(ValidationException::class);
});

it('integrates with InteractsWithForms trait', function () {
    $component = new class
    {
        use InteractsWithForms;

        public function form(): Form
        {
            return SampleContactForm::make();
        }
    };

    $component->initFormState();

    expect($component->state)->toHaveKey('name')
        ->and($component->state)->toHaveKey('email')
        ->and($component->state)->toHaveKey('country')
        ->and($component->state)->toHaveKey('terms')
        ->and($component->state['terms'])->toBeFalse();

    $rules = $component->getFormRules('state');
    expect($rules)->toHaveKey('state.name')
        ->and($rules['state.name'])->toContain('required');

    expect($component->rules())->toEqual($rules);
});

it('triggers real-time validation on updatedInteractsWithForms', function () {
    $component = new class
    {
        use InteractsWithForms;

        public array $validatedOnly = [];

        public function form(): Form
        {
            return SampleContactForm::make();
        }

        public function validateOnly(string $field, array $rules = [], array $messages = [], array $attributes = []): void
        {
            $this->validatedOnly = [
                'field' => $field,
                'rules' => $rules,
                'messages' => $messages,
                'attributes' => $attributes,
            ];
        }
    };

    $component->initFormState();
    $component->updatedInteractsWithForms('state.email');

    expect($component->validatedOnly['field'])->toBe('state.email')
        ->and($component->validatedOnly['rules'])->toHaveKey('state.email');
});

it('supports conditional fields and fieldsets with visibleWhen in Livewire forms', function () {
    $form = new class extends Form
    {
        public function fields(): array
        {
            return [
                Fieldset::make('Account')->fields([
                    Checkbox::make('has_company')->live(),
                    TextInput::make('company_name')->visibleWhen('has_company', true)->required(),
                ]),
                Fieldset::make('Company Details')->visibleWhen('has_company', true)->fields([
                    TextInput::make('vat_number')->required(),
                ]),
            ];
        }
    };

    // When has_company is false:
    $stateFalse = ['has_company' => false, 'company_name' => '', 'vat_number' => ''];
    $htmlFalse = $form->render('state', data: $stateFalse);

    expect($htmlFalse)->not->toContain('wire:model="state.company_name"')
        ->and($htmlFalse)->not->toContain('Company Details')
        ->and($htmlFalse)->not->toContain('wire:model="state.vat_number"');

    $rulesFalse = $form->validationRules('state', $stateFalse);
    expect($rulesFalse)->not->toHaveKey('state.company_name')
        ->and($rulesFalse)->not->toHaveKey('state.vat_number');

    // When has_company is true:
    $stateTrue = ['has_company' => true, 'company_name' => '', 'vat_number' => ''];
    $htmlTrue = $form->render('state', data: $stateTrue);

    expect($htmlTrue)->toContain('wire:model="state.company_name"')
        ->and($htmlTrue)->toContain('Company Details')
        ->and($htmlTrue)->toContain('wire:model="state.vat_number"');

    $rulesTrue = $form->validationRules('state', $stateTrue);
    expect($rulesTrue)->toHaveKey('state.company_name')
        ->and($rulesTrue)->toHaveKey('state.vat_number')
        ->and($rulesTrue['state.company_name'])->toContain('required')
        ->and($rulesTrue['state.vat_number'])->toContain('required');
});
