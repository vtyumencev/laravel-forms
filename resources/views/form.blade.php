@php
    use Erag\InertiaForms\Fields\Submit;
    use Erag\InertiaForms\Fields\Button;
@endphp

@props([
    'form',
    'statePath' => 'state',
    'action' => null,
    'currentStep' => null,
    'data' => null,
])

@php
    $actionName = $action ?? $form->getAction() ?? 'save';
    $isWizard = $form->isWizard();
    $state = $data ?? ((isset($this) && isset($this->{$statePath}) && is_array($this->{$statePath})) ? $this->{$statePath} : ($form->data() ?? []));
    $wizardSteps = $isWizard ? $form->wizardSteps($state) : [];
    $totalSteps = count($wizardSteps);
    $activeStep = $currentStep ?? (isset($this) && isset($this->currentStep) ? $this->currentStep : 0);
    if ($totalSteps > 0) {
        $activeStep = min($activeStep, $totalSteps - 1);
    }
@endphp

<form wire:submit.prevent="{{ $actionName }}" {{ $attributes->merge(['class' => 'space-y-6 ' . ($form->getClass() ?? '')]) }}>
    @if ($isWizard && $totalSteps > 0)
        <div class="mb-6">
            <nav aria-label="Progress">
                <ol role="list" class="flex items-center space-x-2 md:space-x-4">
                    @foreach ($wizardSteps as $index => $stepFieldset)
                        @php
                            $isCurrent = $index === $activeStep;
                            $isCompleted = $index < $activeStep;
                            $stepNumber = $index + 1;
                        @endphp
                        <li class="flex-1">
                            <div class="flex items-center">
                                <div class="flex items-center space-x-2">
                                    <span class="flex items-center justify-center w-8 h-8 rounded-full text-xs font-semibold
                                        @if ($isCompleted)
                                            bg-gamma text-white
                                        @elseif ($isCurrent)
                                            border-2 border-gamma text-gamma
                                        @else
                                            border-2 border-gray-300 text-gray-500
                                        @endif
                                    ">
                                        @if ($isCompleted)
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                      d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                      clip-rule="evenodd"/>
                                            </svg>
                                        @else
                                            {{ $stepNumber }}
                                        @endif
                                    </span>
                                    @if ($stepFieldset->getLegend())
                                        <span class="hidden sm:inline-block text-sm font-medium
                                            @if ($isCurrent)
                                                text-gamma
                                            @elseif ($isCompleted)
                                                text-gray-900
                                            @else
                                                text-gray-500
                                            @endif
                                        ">
                                            {{ $stepFieldset->getLegend() }}
                                        </span>
                                    @endif
                                </div>
                                @if (! $loop->last)
                                    <div class="flex-1 ml-2 md:ml-4 h-0.5 {{ $isCompleted ? 'bg-gamma' : 'bg-gray-200 dark:bg-zinc-700' }}"></div>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ol>
            </nav>
        </div>

        @foreach ($wizardSteps as $index => $stepFieldset)
            @if ($index === $activeStep)
                <div wire:key="wizard-step-{{ $index }}">
                    @include($stepFieldset->getView(), [
                        'fieldset' => $stepFieldset,
                        'statePath' => $statePath,
                        'data' => $state,
                    ])
                </div>
            @endif
        @endforeach

        <div class="flex items-center justify-between pt-4">
            <div>
                @if ($activeStep > 0)
                    @include(config('inertia-forms.components.button', 'forms::fields.button'), [
                        'field' => new Button('previousStep')->actionMethod('previousStep')->label($form->getWizardBackLabel())
                    ])
                @endif
            </div>

            <div class="flex items-center space-x-3">
                @if ($activeStep < $totalSteps - 1)
                    @include(config('inertia-forms.components.button', 'forms::fields.button'), [
                        'field' => new Button('nextStep')->actionMethod('nextStep')->label($form->getWizardNextLabel())->submit()
                    ])
                @elseif (! collect($wizardSteps[$activeStep]->getFields())->contains(fn ($f) => $f instanceof Submit))
                    @include(config('inertia-forms.components.button', 'forms::fields.button'), [
                        'field' => new Button('Submit')->label('Submit')->submit()->processingLabel('Submitting…')
                    ])
                @endif
            </div>
        </div>
    @else
        @foreach ($form->getFieldsets() as $fieldset)
            @if ($fieldset->isAuthorized() && $fieldset->isVisibleFor($state))
                @include($fieldset->getView(), [
                    'fieldset' => $fieldset,
                    'statePath' => $statePath,
                    'data' => $state,
                ])
            @endif
        @endforeach
    @endif
</form>
