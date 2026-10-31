<?php

use Erag\InertiaForms\Concerns\InteractsWithForms;
use Erag\InertiaForms\Fields\Checkbox;
use Erag\InertiaForms\Fields\Fieldset;
use Erag\InertiaForms\Fields\Select;
use Erag\InertiaForms\Fields\Submit;
use Erag\InertiaForms\Fields\Textarea;
use Erag\InertiaForms\Fields\TextInput;
use Erag\InertiaForms\Form;
use Illuminate\Validation\ValidationException;

class WizardSampleForm extends Form
{
    protected bool $wizard = true;

    public function fields(): array
    {
        return [
            Fieldset::make('Personal Information')
                ->columns(2)
                ->fields([
                    TextInput::make('name')
                        ->label('Full Name')
                        ->minLength(5)
                        ->required(),

                    TextInput::make('email')
                        ->label('Email Address')
                        ->email()
                        ->required(),
                ]),

            Fieldset::make('Inquiry Details')
                ->fields([
                    Select::make('topic')
                        ->label('Topic')
                        ->options([
                            'sales' => 'Sales Inquiry',
                            'support' => 'Technical Support',
                        ])
                        ->required(),

                    Textarea::make('message')
                        ->label('Your Message')
                        ->rows(4)
                        ->required(),

                    Checkbox::make('newsletter')
                        ->label('Subscribe to newsletter'),

                    Submit::make('Send Message')
                        ->variant('primary'),
                ]),
        ];
    }
}

class LivewireWizardComponent
{
    use InteractsWithForms;

    public function form(): Form
    {
        return WizardSampleForm::make();
    }
}

it('identifies wizard forms and step fieldsets', function () {
    $form = WizardSampleForm::make();

    expect($form->isWizard())->toBeTrue()
        ->and($form->getWizardNextLabel())->toBe('Continue')
        ->and($form->getWizardBackLabel())->toBe('Back');

    $steps = $form->wizardSteps();
    expect($steps)->toHaveCount(2)
        ->and($steps[0]->getLegend())->toBe('Personal Information')
        ->and($steps[1]->getLegend())->toBe('Inquiry Details');
});

it('extracts step-specific validation rules', function () {
    $form = WizardSampleForm::make();

    $step0Rules = $form->getStepRules(0, 'state');
    expect($step0Rules)->toHaveKey('state.name')
        ->and($step0Rules)->toHaveKey('state.email')
        ->and($step0Rules)->not->toHaveKey('state.topic')
        ->and($step0Rules)->not->toHaveKey('state.message');

    $step1Rules = $form->getStepRules(1, 'state');
    expect($step1Rules)->toHaveKey('state.topic')
        ->and($step1Rules)->toHaveKey('state.message')
        ->and($step1Rules)->not->toHaveKey('state.name');
});

it('navigates through wizard steps with validation in Livewire component', function () {
    $component = new LivewireWizardComponent;
    $component->initFormState();

    expect($component->currentStep)->toBe(0)
        ->and($component->isFirstStep())->toBeTrue()
        ->and($component->isLastStep())->toBeFalse();

    // Step 0 validation fails when required fields are missing
    $component->state['name'] = 'Jane'; // min:5 will fail
    $component->state['email'] = 'not-an-email';

    expect(fn () => $component->nextStep())
        ->toThrow(ValidationException::class);

    expect($component->currentStep)->toBe(0);

    // Step 0 passes with valid data
    $component->state['name'] = 'Jane Doe';
    $component->state['email'] = 'jane@example.com';

    $component->nextStep();

    expect($component->currentStep)->toBe(1)
        ->and($component->isFirstStep())->toBeFalse()
        ->and($component->isLastStep())->toBeTrue();

    // Previous step navigates back without validation error
    $component->previousStep();
    expect($component->currentStep)->toBe(0);

    // Reset wizard resets step counter
    $component->nextStep();
    expect($component->currentStep)->toBe(1);
    $component->resetWizard();
    expect($component->currentStep)->toBe(0);
});

it('renders wizard step navigation, active fieldset and buttons in Blade', function () {
    $form = WizardSampleForm::make();

    // Step 0 render
    $htmlStep0 = $form->render(statePath: 'state', currentStep: 0);

    expect($htmlStep0)->toContain('Personal Information')
        ->and($htmlStep0)->toContain('wire:model="state.name"')
        ->and($htmlStep0)->toContain('wire:model="state.email"')
        ->and($htmlStep0)->toContain('wire:click.prevent="nextStep"')
        ->and($htmlStep0)->toContain('Continue')
        ->and($htmlStep0)->not->toContain('wire:click.prevent="previousStep"');

    // Step 1 render
    $htmlStep1 = $form->render(statePath: 'state', currentStep: 1);

    expect($htmlStep1)->toContain('Inquiry Details')
        ->and($htmlStep1)->toContain('wire:model="state.topic"')
        ->and($htmlStep1)->toContain('wire:model="state.message"')
        ->and($htmlStep1)->toContain('wire:click.prevent="previousStep"')
        ->and($htmlStep1)->toContain('Back')
        ->and($htmlStep1)->toContain('Send Message');
});

it('supports custom wizard next and back labels', function () {
    $form = WizardSampleForm::make()->wizard(true, nextLabel: 'Forward', backLabel: 'Return');

    expect($form->getWizardNextLabel())->toBe('Forward')
        ->and($form->getWizardBackLabel())->toBe('Return');

    $htmlStep0 = $form->render(statePath: 'state', currentStep: 0);
    expect($htmlStep0)->toContain('Forward');

    $htmlStep1 = $form->render(statePath: 'state', currentStep: 1);
    expect($htmlStep1)->toContain('Return');
});

it('supports dynamically visible fieldsets and conditional steps in wizard', function () {
    $form = new class extends Form
    {
        protected bool $wizard = true;

        public function fields(): array
        {
            return [
                Fieldset::make('Account Type')->fields([
                    Select::make('account_type')->options([
                        'personal' => 'Personal',
                        'business' => 'Business',
                    ])->required(),
                ]),
                Fieldset::make('Business Info')->visibleWhen('account_type', 'business')->fields([
                    TextInput::make('company_name')->required(),
                ]),
                Fieldset::make('Confirmation')->fields([
                    Checkbox::make('confirmed')->required(),
                    Submit::make('Finish'),
                ]),
            ];
        }
    };

    // When account_type is personal:
    $statePersonal = ['account_type' => 'personal', 'company_name' => '', 'confirmed' => false];
    $stepsPersonal = $form->wizardSteps($statePersonal);

    expect($stepsPersonal)->toHaveCount(2)
        ->and($stepsPersonal[0]->getLegend())->toBe('Account Type')
        ->and($stepsPersonal[1]->getLegend())->toBe('Confirmation');

    $htmlPersonal = $form->render(statePath: 'state', currentStep: 0, data: $statePersonal);
    expect($htmlPersonal)->toContain('Account Type')
        ->and($htmlPersonal)->toContain('Confirmation')
        ->and($htmlPersonal)->not->toContain('Business Info');

    // When account_type is business:
    $stateBusiness = ['account_type' => 'business', 'company_name' => '', 'confirmed' => false];
    $stepsBusiness = $form->wizardSteps($stateBusiness);

    expect($stepsBusiness)->toHaveCount(3)
        ->and($stepsBusiness[0]->getLegend())->toBe('Account Type')
        ->and($stepsBusiness[1]->getLegend())->toBe('Business Info')
        ->and($stepsBusiness[2]->getLegend())->toBe('Confirmation');

    $htmlBusiness = $form->render(statePath: 'state', currentStep: 0, data: $stateBusiness);
    expect($htmlBusiness)->toContain('Account Type')
        ->and($htmlBusiness)->toContain('Business Info')
        ->and($htmlBusiness)->toContain('Confirmation');

    // Step 1 rules for business info:
    $step1BusinessRules = $form->getStepRules(1, 'state', $stateBusiness);
    expect($step1BusinessRules)->toHaveKey('state.company_name')
        ->and($step1BusinessRules['state.company_name'])->toContain('required');
});
