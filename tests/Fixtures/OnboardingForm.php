<?php

namespace Xenio\LivewireForms\Tests\Fixtures;

use Xenio\LivewireForms\Fields\Fieldset;
use Xenio\LivewireForms\Fields\FileUpload;
use Xenio\LivewireForms\Fields\Submit;
use Xenio\LivewireForms\Fields\Textarea;
use Xenio\LivewireForms\Fields\TextInput;
use Xenio\LivewireForms\Form;

class OnboardingForm extends Form
{
    protected ?string $actionUrl = '/onboarding';

    protected bool $wizard = true;

    public function fields(): array
    {
        return [
            Fieldset::make('Account')->description('Create your login')->icon('user')->fields([
                TextInput::make('name')->required(),
                TextInput::make('email')->email()->required(),
            ]),
            Fieldset::make('Profile')->icon('briefcase')->fields([
                TextInput::make('company')->required(),
                FileUpload::make('logo')->image()->required(),
            ]),
            Fieldset::make('About you')->visibleWhen('company', 'not_empty')->fields([
                Textarea::make('bio')->maxLength(20),
            ]),
            Submit::make('Finish'),
        ];
    }
}
