<?php

namespace Kiwilan\Steward\Traits;

use Illuminate\Support\Str;

trait HasUsername
{
    protected string $default_username_with = 'name';

    protected string $default_username_column = 'username';

    protected bool $default_username_auto_update = true;

    public function initializeHasUsername()
    {
        // $this->fillable[] = $this->getUsernameColumn();
    }

    public function getUsernameWith(): string
    {
        return $this->username_with ?? $this->default_username_with;
    }

    public function getUsernameColumn(): string
    {
        return $this->username_column ?? $this->default_username_column;
    }

    public function getAutoUpdate(): string
    {
        return $this->username_auto_update ?? $this->default_username_auto_update;
    }

    /**
     * Generate a username for Model.
     */
    public function generateUsername(): string
    {
        $tag = rand(1000, 9999);
        $username_name = Str::slug($this->{$this->getUsernameWith()}, '-');
        $username = "{$username_name}-{$tag}";

        $exist = $this::where($this->getUsernameColumn(), $username)->first();

        while ($exist) {
            $tag = $this->generateUsername();
            $username_name = Str::slug($this->{$this->getUsernameWith()}, '-');
            $username = "{$username_name}-{$tag}";
        }

        return Str::slug($username);
    }

    /**
     * Check if attribute link to username is updated.
     */
    public function updateUsername(): string
    {
        $current_username = $this->{$this->getUsernameColumn()};

        $username_name = Str::slug($this->{$this->getUsernameWith()}, '-');
        $username_tag = explode('-', $current_username);
        $username_tag = end($username_tag);

        $new_name = $this->{$this->getUsernameWith()};
        $new_username = "{$new_name}-{$username_tag}";

        $instance = get_class($this);
        $exist = $instance::where($this->getUsernameColumn(), $new_username)->first();

        while ($exist) {
            $username_tag = rand(1000, 9999);
            $new_username = "{$new_name}-{$username_tag}";
            $exist = $instance::where($this->getUsernameColumn(), $new_username)->first();
        }

        return Str::slug($new_username);
    }

    protected static function bootHasUsername()
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getUsernameColumn()})) {
                $model->{$model->getUsernameColumn()} = $model->generateUsername();
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty($model->getUsernameWith())) {
                $model->{$model->getUsernameColumn()} = $model->updateUsername();
            }
        });
    }
}
