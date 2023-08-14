<?php

namespace Kiwilan\Steward\Tests\Data\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property \Kiwilan\Steward\Tests\Data\Models $resource
 */
class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'title' => $this->resource->title,
            'slug_sort' => $this->resource->slug_sort,
        ];
    }
}
