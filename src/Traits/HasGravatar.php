<?php

namespace Kiwilan\Steward\Traits;

use Illuminate\Database\Eloquent\Model;
use Kiwilan\Steward\Services\GravatarService;

/**
 * Trait HasGravatar
 *
 * - Gravatar column: `gravatar`, override with `$gravatarColumn`
 * - Gravatar email column: `email`, override with `$gravatarEmailColumn`
 * - Gravatar default image: `retro`, override with `$gravatarDefaultImage`
 */
trait HasGravatar
{
    protected $defaultGravatarColumn = 'gravatar';

    protected $defaultGravatarEmailColumn = 'email';

    protected $defaultGravatarDefaultImage = 'retro';

    public function initializeHasGravatar()
    {
        $this->fillable[] = $this->getGravatarColumn();
    }

    private function getGravatarColumn(): string
    {
        return $this->gravatarColumn ?? $this->defaultGravatarColumn;
    }

    private function getGravatarEmailColumn(): string
    {
        return $this->gravatarEmailColumn ?? $this->defaultGravatarEmailColumn;
    }

    public function getGravatarDefaultImage(): string
    {
        return $this->gravatarDefaultImage ?? $this->defaultGravatarDefaultImage;
    }

    private function generateGravatar(string $email): string
    {
        return GravatarService::make()
            ->email($email)
            ->size(200)
            ->rating('g')
            ->default($this->getGravatarDefaultImage())
            ->get()
        ;
    }

    public static function bootHasGravatar()
    {
        static::creating(function (Model $model) {
            if ($model->{$model->getGravatarColumn()} === null) {
                $model->{$model->getGravatarColumn()} = $model->generateGravatar($model->{$model->getGravatarEmailColumn()});
            }
        });

        static::updating(function (Model $model) {
            if ($model->isDirty($model->getGravatarEmailColumn())) {
                $model->{$model->getGravatarColumn()} = $model->generateGravatar($model->{$model->getGravatarEmailColumn()});
            }
        });
    }
}
