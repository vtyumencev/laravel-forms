<?php

namespace Xenio\LivewireForms\Fields;

use Xenio\LivewireForms\Support\Icon;
use InvalidArgumentException;

/**
 * The button field. `Button::make('Save')` sets the button text.
 */
class Button extends Field
{

    protected ?string $processingLabel = null;

    protected bool $fullWidth = false;

    protected bool $disableUntilDirty = false;

    protected ?string $actionMethodName = null;

    protected bool $isSubmit = false;

    public function component(): string
    {
        return 'Button';
    }

    public function getLabel(): string
    {
        return $this->label ?? $this->name;
    }

    /**
     * Button text while the form is submitting.
     */
    public function processingLabel(?string $label): static
    {
        $this->processingLabel = $label;

        return $this;
    }

    /**
     * Stretch the button across its container.
     */
    public function fullWidth(bool $fullWidth = true): static
    {
        $this->fullWidth = $fullWidth;

        return $this;
    }

    public function actionMethod(?string $actionMethodName): static
    {
        $this->actionMethodName = $actionMethodName;

        return $this;
    }

    public function getActionMethod(): ?string
    {
        return $this->actionMethodName;
    }

    /**
     * Keep the button disabled until the user changes a value, e.g. a
     * "Save changes" button on an edit form.
     */
    public function disableUntilDirty(bool $disable = true): static
    {
        $this->disableUntilDirty = $disable;

        return $this;
    }

    public function getType(): string
    {
        return 'button';
    }

    public function submit(): static
    {
        $this->isSubmit = true;

        return $this;
    }

    public function isSubmit(): bool
    {
        return $this->isSubmit;
    }

    public function getProcessingLabel(): ?string
    {
        return $this->processingLabel;
    }

    public function isFullWidth(): bool
    {
        return $this->fullWidth;
    }

    public function isDisableUntilDirty(): bool
    {
        return $this->disableUntilDirty;
    }

    public function hasValue(): bool
    {
        return false;
    }

    public function validationRules(): array
    {
        return [];
    }

    protected function props(): array
    {
        return [
            'processingLabel' => $this->processingLabel,
            'fullWidth' => $this->fullWidth,
            'disableUntilDirty' => $this->disableUntilDirty,
        ];
    }
}
