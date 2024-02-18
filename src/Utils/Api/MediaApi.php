<?php

namespace Kiwilan\Steward\Utils\Api;

use Illuminate\Support\Collection;
use Kiwilan\HttpPool\Response\HttpPoolResponse;

interface MediaApi
{
    public function config(...$config): self;

    /**
     * @return Collection<string,HttpPoolResponse>
     */
    public function medias(): Collection;
}
