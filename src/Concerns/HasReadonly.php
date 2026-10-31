<?php

namespace Erag\InertiaForms\Concerns;

use Closure;

trait HasReadonly
{
    /** @var array<int, bool|Closure> */
    protected array $canReadonlyChecks = [];

    /**
     * Include this item only when the check passes.
     */
    public function canReadonly(bool|Closure $check): static
    {
        $this->canReadonlyChecks[] = $check;

        return $this;
    }

    public function canReadonlyWhen(bool|Closure $check): static
    {
        return $this->canReadonly($check);
    }

    public function canReadonlyUnless(bool|Closure $check): static
    {
        return $this->canReadonly(
            $check instanceof Closure ? fn (mixed ...$arguments): bool => ! $check(...$arguments) : ! $check,
        );
    }

    public function isCanReadonly(): bool
    {
        foreach ($this->canReadonlyChecks as $check) {
            if ((bool) value($check, $this)) {
                return true;
            }
        }

        return false;
    }
}
