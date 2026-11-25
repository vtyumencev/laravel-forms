<?php

namespace Xenio\LivewireForms\Fields;

/**
 * The submit button. `Submit::make('Save')` sets the button text.
 */
class Submit extends Button
{
    public function component(): string
    {
        return 'Submit';
    }

    protected ?string $intentKey = null;

    protected ?string $intentValue = null;

    /**
     * @return array{key: string, value: string}|null
     */
    public function getIntent(): ?array
    {
        return $this->intentKey === null || $this->intentValue === null
            ? null
            : ['key' => $this->intentKey, 'value' => $this->intentValue];
    }
}
