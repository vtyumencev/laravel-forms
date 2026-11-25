<?php

namespace Xenio\LivewireForms\Fields;

use Xenio\LivewireForms\Support\Icon;

/**
 * A notice inside the form: `Summary::make()`.
 */
class Summary extends DisplayField
{
    public static function make(string $name = '', ?string $body = null): static
    {
        return parent::make($name);
    }

    public function component(): string
    {
        return 'Summary';
    }

    public function content(string $content): static
    {
        return $this;
    }
}
