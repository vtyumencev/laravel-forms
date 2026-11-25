<?php

namespace Xenio\LivewireForms\Concerns;

use Xenio\LivewireForms\Fields\Heading;
use Xenio\LivewireForms\Form;

trait InteractsWithForms
{
    public array $state = [];

    public int $currentStep = 0;

    public function updatedWizardStep(): void
    {
    }

    public function mountInteractsWithForms(): void
    {
        $this->initFormState();
    }

    public function updatedInteractsWithForms(string $propertyName): void
    {
        $this->validateOnlyFormProperty($propertyName);
    }

    public function validateOnlyFormProperty(string $propertyName, string $stateProperty = 'state', ?string $formMethod = 'form'): void
    {
        if (str_starts_with($propertyName, "{$stateProperty}.") || $propertyName === $stateProperty) {
            if (method_exists($this, 'validateOnly')) {
                $rules = $this->getFormRules($stateProperty, $formMethod);
                $messages = $this->getFormValidationMessages($stateProperty, $formMethod);
                $attributes = $this->getFormValidationAttributes($stateProperty, $formMethod);

                if (! empty($rules)) {
                    $this->validateOnly($propertyName, $rules, $messages, $attributes);
                }
            }
        }
    }

    public function rules(): array
    {
        return $this->getFormRules();
    }

    public function messages(): array
    {
        return $this->getFormValidationMessages();
    }

    public function validationAttributes(): array
    {
        return $this->getFormValidationAttributes();
    }

    public function initFormState(?string $formMethod = 'form', string $stateProperty = 'state'): void
    {
        if (method_exists($this, $formMethod)) {
            /** @var Form $form */
            $form = $this->{$formMethod}();
            $data = $form->initialValues();
            foreach ($data as $key => $val) {
                if (! array_key_exists($key, $this->{$stateProperty})) {
                    $this->{$stateProperty}[$key] = $val;
                }
            }
        }
    }

    public function getFormRules(string $stateProperty = 'state', ?string $formMethod = 'form'): array
    {
        if (method_exists($this, $formMethod)) {
            $this->protectFormState($stateProperty, $formMethod);
            /** @var Form $form */
            $form = $this->{$formMethod}();

            return $form->validationRules($stateProperty, $this->{$stateProperty} ?? []);
        }

        return [];
    }

    public function getFormValidationMessages(string $stateProperty = 'state', ?string $formMethod = 'form'): array
    {
        if (method_exists($this, $formMethod)) {
            /** @var Form $form */
            $form = $this->{$formMethod}();

            return $form->getValidationMessages($stateProperty, $this->{$stateProperty} ?? []);
        }

        return [];
    }

    public function getFormValidationAttributes(string $stateProperty = 'state', ?string $formMethod = 'form'): array
    {
        if (method_exists($this, $formMethod)) {
            /** @var Form $form */
            $form = $this->{$formMethod}();

            return $form->getValidationAttributes($stateProperty, $this->{$stateProperty} ?? []);
        }

        return [];
    }

    public function protectFormState(string $stateProperty = 'state', ?string $formMethod = 'form')
    {
        if (method_exists($this, $formMethod) && isset($this->{$stateProperty}) && is_array($this->{$stateProperty})) {
            /** @var Form $form */
            $form = $this->{$formMethod}();
            $this->{$stateProperty} = $form->protectCanReadonlyValues($this->{$stateProperty});
        }
    }

    public function validateForm(string $stateProperty = 'state', ?string $formMethod = 'form'): array
    {
        $this->protectFormState($stateProperty, $formMethod);
        $rules = $this->getFormRules($stateProperty, $formMethod);
        $messages = $this->getFormValidationMessages($stateProperty, $formMethod);
        $attributes = $this->getFormValidationAttributes($stateProperty, $formMethod);

        if (method_exists($this, 'validate')) {
            return $this->validate($rules, $messages, $attributes);
        }

        if (method_exists($this, $formMethod)) {
            /** @var Form $form */
            $form = $this->{$formMethod}();

            return $form->validateData($this->{$stateProperty}, $stateProperty);
        }

        return [];
    }

    public function nextStep(string $stateProperty = 'state', ?string $formMethod = 'form'): void
    {
        $this->validateStep($this->currentStep, $stateProperty, $formMethod);

        if (method_exists($this, $formMethod)) {
            /** @var Form $form */
            $form = $this->{$formMethod}();
            $steps = $form->wizardSteps($this->{$stateProperty} ?? []);
            if ($this->currentStep < count($steps) - 1) {
                $this->currentStep++;
            }
        } else {
            $this->currentStep++;
        }

        $this->updatedWizardStep($this->currentStep);
    }

    public function previousStep(): void
    {
        if ($this->currentStep > 0) {
            $this->currentStep--;
        }

        $this->updatedWizardStep($this->currentStep);
    }

    public function goToStep(int $step, string $stateProperty = 'state', ?string $formMethod = 'form'): void
    {
        if ($step < $this->currentStep) {
            $this->currentStep = max(0, $step);

            return;
        }

        if ($step > $this->currentStep) {
            $this->validateStep($this->currentStep, $stateProperty, $formMethod);

            if (method_exists($this, $formMethod)) {
                /** @var Form $form */
                $form = $this->{$formMethod}();
                $steps = $form->wizardSteps($this->{$stateProperty} ?? []);
                $this->currentStep = min($step, max(0, count($steps) - 1));
            } else {
                $this->currentStep = max(0, $step);
            }
        }
    }

    public function validateStep(?int $step = null, string $stateProperty = 'state', ?string $formMethod = 'form'): array
    {
        $this->protectFormState($stateProperty, $formMethod);
        $step ??= $this->currentStep;

        if (method_exists($this, $formMethod)) {
            /** @var Form $form */
            $form = $this->{$formMethod}();
            $rules = $form->getStepRules($step, $stateProperty, $this->{$stateProperty} ?? []);
            $messages = $form->getStepValidationMessages($step, $stateProperty, $this->{$stateProperty} ?? []);
            $attributes = $form->getStepValidationAttributes($step, $stateProperty, $this->{$stateProperty} ?? []);

            if (method_exists($this, 'validate')) {
                if (! empty($rules)) {
                    return $this->validate($rules, $messages, $attributes);
                }

                return [];
            }

            return $form->validateStepData($step, $this->{$stateProperty} ?? [], $stateProperty);
        }

        return [];
    }

    public function getStepRules(?int $step = null, string $stateProperty = 'state', ?string $formMethod = 'form'): array
    {
        $step ??= $this->currentStep;

        if (method_exists($this, $formMethod)) {
            /** @var Form $form */
            $form = $this->{$formMethod}();

            return $form->getStepRules($step, $stateProperty, $this->{$stateProperty} ?? []);
        }

        return [];
    }

    public function getStepValidationMessages(?int $step = null, string $stateProperty = 'state', ?string $formMethod = 'form'): array
    {
        $step ??= $this->currentStep;

        if (method_exists($this, $formMethod)) {
            /** @var Form $form */
            $form = $this->{$formMethod}();

            return $form->getStepValidationMessages($step, $stateProperty, $this->{$stateProperty} ?? []);
        }

        return [];
    }

    public function getStepValidationAttributes(?int $step = null, string $stateProperty = 'state', ?string $formMethod = 'form'): array
    {
        $step ??= $this->currentStep;

        if (method_exists($this, $formMethod)) {
            /** @var Form $form */
            $form = $this->{$formMethod}();

            return $form->getStepValidationAttributes($step, $stateProperty, $this->{$stateProperty} ?? []);
        }

        return [];
    }

    public function isFirstStep(): bool
    {
        return $this->currentStep === 0;
    }

    public function isLastStep(string $stateProperty = 'state', ?string $formMethod = 'form'): bool
    {
        if (method_exists($this, $formMethod)) {
            /** @var Form $form */
            $form = $this->{$formMethod}();
            $steps = $form->wizardSteps($this->{$stateProperty} ?? []);

            return $this->currentStep >= count($steps) - 1;
        }

        return true;
    }

    public function getWizardSteps(string $stateProperty = 'state', ?string $formMethod = 'form'): array
    {
        if (method_exists($this, $formMethod)) {
            /** @var Form $form */
            $form = $this->{$formMethod}();

            return $form->wizardSteps($this->{$stateProperty} ?? []);
        }

        return [];
    }

    public function isWizardForm(?string $formMethod = 'form'): bool
    {
        if (method_exists($this, $formMethod)) {
            /** @var Form $form */
            $form = $this->{$formMethod}();

            return $form->isWizard();
        }

        return false;
    }

    public function resetWizard(): void
    {
        $this->updatedWizardStep(0);
        $this->currentStep = 0;
    }

    public function getFormSummary(
        string $stateProperty = 'state',
        ?string $formMethod = 'form',
    ): array {
        if (! method_exists($this, $formMethod)) {
            return [];
        }

        // Restore server-authoritative values for canReadonly() fields.
        $this->protectFormState($stateProperty, $formMethod);

        /** @var \Xenio\LivewireForms\Form $form */
        $form = $this->{$formMethod}();
        $state = $this->{$stateProperty} ?? [];
        $summary = [];

        foreach ($form->getFieldsets() as $fieldset) {
            if (! $fieldset->isAuthorized() || ! $fieldset->isVisibleFor($state)) {
                continue;
            }

            foreach ($fieldset->getFields() as $field) {
                if (! $field->isAuthorized() || ! $field->isVisibleFor($state)) {
                    continue;
                }

                if ($field instanceof Heading) {
                    $summary[] = [
                        'type' => 'heading',
                        'label' => $field->getText(),
                        'value' => null,
                    ];

                    continue;
                }

                if (! $field->hasValue()) {
                    continue;
                }

                $value = data_get($state, $field->getName());

                $summary[] = [
                    'type' => 'input',
                    'label' => $field->getLabel(),
                    'value' => $this->formatSummaryValue($value),
                ];
            }
        }

        return $summary;
    }

    protected function formatSummaryValue(mixed $value): mixed
    {
        if (is_bool($value)) {
            return $value ? 'Ja' : 'Nein';
        }

        if (is_array($value)) {
            return implode(', ', array_map(
                fn (mixed $item): string => (string) $item,
                $value,
            ));
        }

        return $value;
    }
}
