<?php

namespace Xenio\LivewireForms\Fields;

use Xenio\LivewireForms\Support\Icon;

/**
 * A notice inside the form: `Callout::make('Review required', 'Confirm the billing contact.')->warning()`.
 */
class Callout extends DisplayField
{
    public const array TONES = ['info', 'success', 'warning', 'danger'];

    protected string $title = '';

    protected ?string $body = null;

    protected string $tone = 'info';

    protected ?string $icon = 'circleInfo';

    public static function make(string $name = '', ?string $body = null): static
    {
        return parent::make($name)->body($body);
    }

    public function component(): string
    {
        return 'Callout';
    }

    public function content(string $content): static
    {
        $this->title = $content;

        return $this;
    }

    public function title(string $title): static
    {
        return $this->content($title);
    }

    public function body(?string $body): static
    {
        $this->body = $body;

        return $this;
    }

    /**
     * info, success, warning or danger.
     */
    public function tone(string $tone): static
    {
        $this->tone = in_array($tone, self::TONES, true) ? $tone : 'info';

        return $this;
    }

    public function info(): static
    {
        return $this->tone('info');
    }

    public function success(): static
    {
        return $this->tone('success');
    }

    public function warning(): static
    {
        return $this->tone('warning');
    }

    public function danger(): static
    {
        return $this->tone('danger');
    }

    /**
     * An icon name, like `user`. Defaults to an icon that matches the tone.
     */
    public function icon(?string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function getIcon(): ?string
    {
        return Icon::svg($this->icon);
    }


    public function getTone(): ?string
    {
        return $this->tone;
    }
}
