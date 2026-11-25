<?php

namespace Xenio\LivewireForms\Fields;

use Xenio\LivewireForms\Fields\Concerns\HasBooleanValues;

class Checkbox extends Field
{
    use HasBooleanValues;

    public function component(): string
    {
        return 'Checkbox';
    }

    protected function props(): array
    {
        return $this->booleanProps();
    }
}
