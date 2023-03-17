<?php

namespace Kiwilan\Steward\Services\Http\Utils;

use Illuminate\Database\Eloquent\Model;

/**
 * HttpQuery from Model.
 *
 * @property ?Model  $model
 * @property ?string $model_name
 * @property ?int    $model_id
 */
abstract class HttpQuery
{
    public function __construct(
        public ?Model $model = null,
        public ?string $model_name = null,
        public ?int $model_id = 0,
    ) {
    }
}
