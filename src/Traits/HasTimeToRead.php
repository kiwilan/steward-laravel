<?php

namespace Kiwilan\Steward\Traits;

/**
 * Trait HasTimeToRead
 *
 * - Default time to read with is `body`, can be override by setting `$timeToReadWith` property
 * - Default time to read column is `time_to_read`, can be override by setting `$timeToReadColumn` property
 *
 * ```php
 * class Post extends Model
 * {
 *    use HasTimeToRead;
 *
 *   protected $timeToReadWith = 'body';
 *   protected $timeToReadColumn = 'time_to_read';
 * }
 * ```
 */
trait HasTimeToRead
{
    protected $defaultTimeToReadWith = 'body';

    protected $defaultTimeToReadColumn = 'time_to_read';

    public function initializeHasTimeToRead()
    {
        $this->fillable[] = $this->getTimeToReadColumn();
        $this->appends[] = 'time_to_read_minutes';
        $this->casts[$this->getTimeToReadColumn()] = 'integer';
    }

    public function getTimeToReadWith(): string
    {
        return $this->timeToReadWith ?? $this->defaultTimeToReadWith;
    }

    public function getTimeToReadColumn(): string
    {
        return $this->timeToReadColumn ?? $this->defaultTimeToReadColumn;
    }

    protected static function bootHasTimeToRead()
    {
        static::creating(function ($model) {
            if (empty($model->time_to_read)) {
                $model->{$model->getTimeToReadColumn()} = $model->getTimeToRead();
            }
        });
    }

    public function getTimeToRead(): int
    {
        $words = strip_tags($this->{$this->getTimeToReadWith()});
        $word_count = str_word_count($words);
        $minutes_to_read = round($word_count / 200);

        if ($minutes_to_read < 1) {
            $minutes_to_read = 1;
        }

        return intval($minutes_to_read * 60);
    }

    public function getTimeToReadMinutesAttribute(): int
    {
        return intval($this->{$this->getTimeToReadColumn()} / 60);
    }
}
