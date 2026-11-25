<?php

namespace Xenio\LivewireForms\Tests\Fixtures;

use Xenio\LivewireForms\Fields\Checkbox;
use Xenio\LivewireForms\Fields\Fieldset;
use Xenio\LivewireForms\Fields\Select;
use Xenio\LivewireForms\Fields\Submit;
use Xenio\LivewireForms\Fields\TextInput;
use Xenio\LivewireForms\Form;

class ContactForm extends Form
{
    protected ?string $actionRoute = 'contacts.store';

    public function fields(): array
    {
        return [
            TextInput::make('name')->required()->maxLength(50),
            TextInput::make('email')->email()->required(),
            Fieldset::make('Preferences')->columns(2)->fields([
                Select::make('contact_method')->options(['email' => 'Email', 'phone' => 'Phone'])->default('email'),
                TextInput::make('phone')->required()->visibleWhen('contact_method', 'phone'),
                Select::make('plan')->options(Plan::class),
                Checkbox::make('terms')->label('I agree')->rule('accepted'),
            ]),
            TextInput::make('salary')->number()->authorizedWhen(fn (): bool => false),
            Submit::make('Send'),
        ];
    }
}
