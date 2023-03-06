<?php

namespace Kiwilan\Steward\Services\Http;

use Illuminate\Database\Eloquent\Model;

/**
 * HttpModelQuery from Model.
 *
 * @property ?Model  $model
 * @property ?string $model_name
 * @property ?int    $model_id
 */
abstract class HttpModelQuery
{
    public function __construct(
        public ?Model $model = null,
        public ?string $model_name = null,
        public ?int $model_id = 0,
    ) {
    }
}
