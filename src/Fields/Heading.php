<?php

namespace Xenio\LivewireForms\Fields;

/**
 * A heading inside the form, e.g. `Heading::make('Billing details')->level(3)`.
 */
class Heading extends DisplayField
{
    protected string $text = '';

    protected int $level = 3;

    public function component(): string
    {
        return 'Heading';
    }

    public function content(string $content): static
    {
        $this->text = $content;

        return $this;
    }

    public function text(string $text): static
    {
        return $this->content($text);
    }

    /**
     * The heading level, from 1 (`h1`) to 4 (`h4`).
     */
    public function level(int $level): static
    {
        $this->level = min(max($level, 1), 4);

        return $this;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    public function getLevel(): int
    {
        return $this->level;
    }
}
