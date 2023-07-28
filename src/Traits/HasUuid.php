<?php

namespace Kiwilan\Steward\Traits;

/**
 * Trait HasUuid
 *
 * Default column is `uuid`.
 *
 * You can override it by setting `protected $uuidColumn = 'column';` property.
 */
trait HasUuid
{
    protected $defaultUuidColumn = 'uuid';

    public function initializeHasUuid()
    {
        $this->fillable[] = $this->getUuidColumn();
    }

    public function getUuidColumn(): string
    {
        return $this->uuidColumn ?? $this->defaultUuidColumn;
    }

    protected static function bootHasUuid()
    {
        static::creating(function ($model) {
            $model->{$model->getUuidColumn()} = self::generateUuid();
        });
    }

    public static function generateUuid(): string
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand(0, 0xFFFF), mt_rand(0, 0xFFFF),

            // 16 bits for "time_mid"
            mt_rand(0, 0xFFFF),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0FFF) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3FFF) | 0x8000,

            // 48 bits for "node"
            mt_rand(0, 0xFFFF), mt_rand(0, 0xFFFF), mt_rand(0, 0xFFFF)
        );
    }
}
