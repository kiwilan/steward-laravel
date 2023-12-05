<?php

namespace Kiwilan\Steward\Filament\Components;

use Filament\Forms\Components\Field;

class ActionButton extends Field
{
    protected string $view = 'steward::filament.field.action-button';

    protected string $url;

    public function url(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
