<?php

namespace Kiwilan\Steward\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property \App\Models\TagExtend $resource
 */
class DefaultResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'error' => 'No resource found',
        ];
    }
}
