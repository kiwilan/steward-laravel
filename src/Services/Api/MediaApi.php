<?php

namespace Kiwilan\Steward\Services\Api\Seeds;

use Illuminate\Support\Collection;
use Kiwilan\Steward\Services\Http\HttpResponse;

interface MediaApi
{
    public function config(...$config): self;

    /**
     * @return Collection<string,HttpResponse>
     */
    public function medias(): Collection;
}
