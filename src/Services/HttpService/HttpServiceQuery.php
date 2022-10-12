<?php

namespace Kiwilan\Steward\Services\HttpService;

use Illuminate\Database\Eloquent\Model;

/**
 * HttpServiceQuery from Model.
 *
 * @property ?Model  $model
 * @property ?string $model_name
 * @property ?int    $model_id
 */
abstract class HttpServiceQuery
{
    public function __construct(
        public ?Model $model = null,
        public ?string $model_name = null,
        public ?int $model_id = 0,
    ) {
    }
}
