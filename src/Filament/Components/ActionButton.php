<?php

namespace Kiwilan\Steward\Filament\Components;

use Filament\Forms\Components\Field;

class ActionButton extends Field
{
    protected string $view = 'steward::filament.field.action-button';

    protected string $download;

    /**
     * Add URL for file to download.
     */
    public function download(string $download): static
    {
        $this->download = $download;

        return $this;
    }

    public function getDownload(): string
    {
        $this->isLabelHidden = true;

        return $this->download;
    }
}
