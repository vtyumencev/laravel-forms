<?php

namespace Xenio\LivewireForms\Concerns;

use Closure;

trait HasLimitedFields
{
    /** @var array<int, string> */
    protected array $limitedFields = [];

    public function limitedFields($fields): static
    {
        $this->limitedFields = $fields;

        return $this;
    }

    public function isFieldLimited($fieldName): bool
    {
        return in_array($fieldName, $this->limitedFields);
    }
}
