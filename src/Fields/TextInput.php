<?php

namespace Erag\InertiaForms\Fields;

use Illuminate\View\ComponentAttributeBag;

class TextInput extends Field
{
    protected string $type = 'text';

    protected ?int $minLength = null;

    protected ?int $maxLength = null;

    protected int|float|null $min = null;

    protected int|float|null $max = null;

    protected int|float|string|null $step = null;

    protected ?string $prefix = null;

    protected ?string $suffix = null;

    protected ?string $autocomplete = null;

    public function component(): string
    {
        return 'TextInput';
    }

    public function type(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function email(): static
    {
        return $this->type('email');
    }

    public function password(): static
    {
        return $this->type('password');
    }

    public function number(): static
    {
        return $this->type('number');
    }

    public function url(): static
    {
        return $this->type('url');
    }

    public function tel(): static
    {
        return $this->type('tel');
    }

    public function search(): static
    {
        return $this->type('search');
    }

    public function minLength(?int $length): static
    {
        $this->minLength = $length;

        return $this;
    }

    public function maxLength(?int $length): static
    {
        $this->maxLength = $length;

        return $this;
    }

    public function min(int|float|null $value): static
    {
        $this->min = $value;

        return $this;
    }

    public function max(int|float|null $value): static
    {
        $this->max = $value;

        return $this;
    }

    public function step(int|float|string|null $step): static
    {
        $this->step = $step;

        return $this;
    }

    /**
     * Text shown inside the input before the value, like `$` or `https://`.
     */
    public function prefix(?string $prefix): static
    {
        $this->prefix = $prefix;

        return $this;
    }

    public function suffix(?string $suffix): static
    {
        $this->suffix = $suffix;

        return $this;
    }

    public function autocomplete(?string $autocomplete): static
    {
        $this->autocomplete = $autocomplete;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getMin(): int|float|null
    {
        return $this->min;
    }

    public function getMax(): int|float|null
    {
        return $this->max;
    }

    public function getStep(): int|float|string|null
    {
        return $this->step;
    }

    public function getPrefix(): ?string
    {
        return $this->prefix;
    }

    public function getSuffix(): ?string
    {
        return $this->suffix;
    }

    public function getAutocomplete(): ?string
    {
        return $this->autocomplete;
    }

    public function getMinLength(): ?int
    {
        return $this->minLength;
    }

    public function getMaxLength(): ?int
    {
        return $this->maxLength;
    }

    protected function typeRules(): array
    {
        if ($this->type === 'number') {
            return array_values(array_filter([
                'numeric',
                $this->min !== null ? "min:{$this->min}" : null,
                $this->max !== null ? "max:{$this->max}" : null,
            ]));
        }

        return array_values(array_filter([
            'string',
            match ($this->type) {
                'email' => 'email',
                'url' => 'url',
                default => null,
            },
            $this->minLength !== null ? "min:{$this->minLength}" : null,
            $this->maxLength !== null ? "max:{$this->maxLength}" : null,
        ]));
    }

    protected function props(): array
    {
        return [
            'type' => $this->type,
            'minLength' => $this->minLength,
            'maxLength' => $this->maxLength,
            'min' => $this->min,
            'max' => $this->max,
            'step' => $this->step,
            'prefix' => $this->prefix,
            'suffix' => $this->suffix,
            'autocomplete' => $this->autocomplete ?? ($this->type === 'password' ? 'current-password' : null),
        ];
    }

    /**
     * @return ComponentAttributeBag
     */
    public function getInputAttributes($statePath = 'state'): ComponentAttributeBag
    {
        $attributes = new ComponentAttributeBag();

        $wireModel = $this->wireModelName($statePath);
        $modifier = $this->getLivewireModifier();
        $binding = 'wire:model' . ($modifier ? '.' . $modifier : '');

        $attributes->setAttributes([
            'type' => $this->getType(),
            'placeholder' => $this->getPlaceholder(),
            'readonly' => $this->isReadonly(),
            'disabled' => $this->isDisabled(),
            'autofocus' => $this->isAutofocus(),
            'min' => $this->getMin(),
            'max' => $this->getMax(),
            'step' => $this->getStep(),

            $binding => $wireModel,
            'id' => $wireModel,
        ]);

        return $attributes;
    }
}
