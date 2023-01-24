<?php

namespace Kiwilan\Steward\Traits;

trait HasTimeToRead
{
    protected $default_time_to_read_with = 'body';

    protected $default_time_to_read_column = 'time_to_read';

    public function initializeHasTimeToRead()
    {
        $this->fillable[] = $this->getTimeToReadColumn();

        $this->casts[$this->getTimeToReadColumn()] = 'integer';
    }

    public function getTimeToReadWith(): string
    {
        return $this->time_to_read_with ?? $this->default_time_to_read_with;
    }

    public function getTimeToReadColumn(): string
    {
        return $this->time_to_read_column ?? $this->default_time_to_read_column;
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
        $word_count = str_word_count($this->{$this->getTimeToReadWith()});
        $minutes_to_read = round($word_count / 200);

        if ($minutes_to_read < 1) {
            $minutes_to_read = 1;
        }

        return intval($minutes_to_read * 60);
    }
}
