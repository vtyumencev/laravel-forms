@php
    use Xenio\LivewireForms\Fields\Callout;
    use Xenio\LivewireForms\Fields\Submit;
    use Xenio\LivewireForms\Fields\Button;
    use Xenio\LivewireForms\Form;
    use Livewire\Component;
@endphp

@props([
    'form',
    'statePath' => 'state',
    'action' => null,
    'currentStep' => null,
    'data' => null,
])

@php
    /**
    * @var Form $form
    * @var Component $this
    */

    $actionName = $action ?? $form->getAction() ?? 'save';
    $isWizard = $form->isWizard();
    $state = $data ?? ((isset($this) && isset($this->{$statePath}) && is_array($this->{$statePath})) ? $this->{$statePath} : ($form->data() ?? []));
    $wizardSteps = $isWizard ? $form->wizardSteps($state) : [];
    $wizardSubmitButtons = $isWizard ? $form->wizardSubmitButtons() : [];
    $totalSteps = count($wizardSteps);
    $activeStep = $currentStep ?? (isset($this) && isset($this->currentStep) ? $this->currentStep : 0);
    if ($totalSteps > 0) {
        $activeStep = min($activeStep, $totalSteps - 1);
    }
@endphp

<form class="relative"
      x-data="livewireForm"
      wire:submit.prevent="{{ $actionName }}" {{ $attributes->merge(['class' => 'space-y-6 ' . ($form->getClass() ?? '')]) }}>
    <script>
        Alpine.data('livewireForm', () => ({
            init() {
                this.$wire.$on('wizard-step-updated', () => this.scrollIntoView(this.$wire.$el));
            }
        }))
    </script>
    @if ($isWizard && $totalSteps > 0)
        <div class="form-steps mb-theme-sm" x-bind:style="{'--progress': {{ $activeStep + 1 }} / {{ $totalSteps }} }">
            <div class="form-steps__progress-label">
                Schritt {{ $activeStep + 1 }} von {{ $totalSteps }}
            </div>
            <div class="form-steps__progress-line"></div>
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

        @if($this->getErrorBag()->any())
            <div class="my-4">
                @include(config('livewire-forms.components.callout', 'forms::fields.callout'), [
                    'field' => Callout::make('Es wurden einige Probleme gefunden')->danger()->icon('circleAlert')->body(implode('<br>', $this->getErrorBag()->all()))
                ])
            </div>
        @endif

        @if (session()->has('error'))
            <div class="my-4">
                @include(config('livewire-forms.components.callout', 'forms::fields.callout'), [
                    'field' => Callout::make(session('error'))->icon('circleAlert')->danger()
                ])
            </div>
        @endif

        <div class="flex items-center justify-between mt-4">
            <div>
                @if ($activeStep > 0)
                    @include(config('livewire-forms.components.button', 'forms::fields.button'), [
                        'field' => new Button('previousStep')->actionMethod('previousStep')->label($form->getWizardBackLabel())
                    ])
                @endif
            </div>

            <div class="flex items-center space-x-3">
                @if ($activeStep < $totalSteps - 1)
                    @include(config('livewire-forms.components.button', 'forms::fields.button'), [
                        'field' => new Submit('nextStep')->actionMethod('nextStep')->label($form->getWizardNextLabel())->submit()
                    ])
                @elseif($wizardSubmitButtons->isNotEmpty())
                    @foreach($wizardSubmitButtons as $wizardSubmitButton)
                        @include(config('livewire-forms.components.button', 'forms::fields.button'), [
                            'field' => $wizardSubmitButton
                        ])
                    @endforeach
                @else
                    @include(config('livewire-forms.components.button', 'forms::fields.button'), [
                        'field' => new Submit('Submit')->label('Submit')->submit()->processingLabel('Submitting…')
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
