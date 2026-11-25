<?php

namespace Xenio\LivewireForms\Concerns;

use Closure;
use Xenio\LivewireForms\Support\Condition;

trait HasVisibility
{
    /** @var array<int, Condition|Closure> */
    protected array $visibilityConditions = [];

    /**
     * Show only when another field matches or closure returns true. Pass a value, or an operator and value.
     */
    public function visibleWhen(string|Closure $field, mixed ...$arguments): static
    {
        if ($field instanceof Closure) {
            $this->visibilityConditions[] = $field;

            return $this;
        }

        $this->visibilityConditions[] = Condition::fromArguments($field, $arguments);

        return $this;
    }

    /**
     * Hide when another field matches or closure returns true. Pass a value, or an operator and value.
     */
    public function hiddenWhen(string|Closure $field, mixed ...$arguments): static
    {
        if ($field instanceof Closure) {
            $this->visibilityConditions[] = fn (array $data): bool => ! (bool) $field($data);

            return $this;
        }

        $this->visibilityConditions[] = Condition::fromArguments($field, $arguments, negate: true);

        return $this;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function isVisibleFor(array $data): bool
    {
        foreach ($this->visibilityConditions as $condition) {
            if ($condition instanceof Closure) {
                if (! (bool) $condition($data)) {
                    return false;
                }

                continue;
            }

            if (! $condition->passes($data)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array<int, Condition|Closure>|null
     */
    protected function serializeVisibility(): ?array
    {
        return $this->visibilityConditions === [] ? null : $this->visibilityConditions;
    }
}
