<?php

namespace Kiwilan\Steward\Traits;

use Illuminate\Database\Eloquent\Model;

/**
 * `$gravatarDefaultImage` can be:
 * - 404: do not load any image if none is associated with the email hash, instead return an HTTP 404 (File Not Found) response
 * - mp: (mystery-person) a simple, cartoon-style silhouetted outline of a person (does not vary by email hash)
 * - identicon: a geometric pattern based on an email hash
 * - monsterid: a generated 'monster' with different colors, faces, etc
 * - wavatar: generated faces with differing features and backgrounds
 * - retro: awesome generated, 8-bit arcade-style pixelated faces
 * - robohash: a generated robot with different colors, faces, etc
 * - blank: a transparent PNG image (border added to HTML below for demonstration purposes)
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
        $hash = md5(strtolower(trim($email)));
        $defaultGravatarSize = 200;
        $defaultGravatarRating = 'g';
        $defaultGravatarDefault = $this->getGravatarDefaultImage();

        return "https://www.gravatar.com/avatar/{$hash}?s={$defaultGravatarSize}&r={$defaultGravatarRating}&d={$defaultGravatarDefault}";
    }

    public static function bootHasGravatar()
    {
        static::creating(function (Model $model) {
            if (null === $model->{$model->getGravatarColumn()}) {
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
