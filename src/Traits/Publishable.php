<?php

namespace Kiwilan\Steward\Traits;

use BackedEnum;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Kiwilan\Steward\Enums\PublishStatusEnum;

/**
 * Trait Publishable.
 *
 * @property BackedEnum $status       can be override by `publishableStatus`
 * @property DateTime $published_at can be override by `publishablePublishedAt`
 *
 * @method void publish() Publish the model
 * @method void unpublish() Unpublish the model
 * @method Builder published() Get all models where `status` is `published`
 * @method Builder scheduled() Get all models where `status` is `scheduled`
 * @method Builder drafted() Get all models where `status` is `draft`
 * @method Builder shouldBePublished() Get all models where `status` is `published` and `published_at` is less than now
 *
 * ```php
 * // migration
 * use Kiwilan\Steward\Enums\PublishStatusEnum;
 *
 * $table->enum('status', PublishStatusEnum::toDatabase())->default(PublishStatusEnum::draft->value);
 * $table->dateTime('published_at')->nullable();
 * ```
 *
 * ```php
 * use Kiwilan\Steward\Traits\Publishable;
 *
 * class Post extends Model
 * {
 *    use Publishable;
 *
 *   protected $publishableStatus = 'status_custom'; // default is `status`
 *   protected $publishablePublishedAt = 'published_at_custom'; // default is `published_at`
 * }
 * ```
 */
trait Publishable
{
    protected $publishableStatusDefault = 'status';

    protected $publishablePublishedAtDefault = 'published_at';

    public function initializePublishable()
    {
        $this->fillable[] = $this->getPublishableStatus();
        $this->fillable[] = $this->getPublishablePublishedAt();

        $this->casts[$this->getPublishableStatus()] = PublishStatusEnum::class;
        $this->casts[$this->getPublishablePublishedAt()] = 'datetime:Y-m-d';
    }

    public function getPublishableStatus(): string
    {
        return $this->publishableStatus ?? $this->publishableStatusDefault;
    }

    public function getPublishablePublishedAt(): string
    {
        return $this->publishablePublishedAt ?? $this->publishablePublishedAtDefault;
    }

    public function publish(): void
    {
        $this->{$this->getPublishableStatus()} = PublishStatusEnum::published;
        $this->{$this->getPublishablePublishedAt()} = Carbon::now();
        $this->save();
    }

    public function unpublish(): void
    {
        $this->{$this->getPublishableStatus()} = PublishStatusEnum::draft;
        $this->{$this->getPublishablePublishedAt()} = null;
        $this->save();
    }

    public function scopePublished(Builder $builder): Builder
    {
        return $builder
            ->where($this->getPublishableStatus(), PublishStatusEnum::published)
            ->where($this->getPublishablePublishedAt(), '<=', Carbon::now())
        ;
    }

    public function scopeScheduled(Builder $builder): Builder
    {
        return $builder
            ->where($this->getPublishableStatus(), PublishStatusEnum::scheduled)
        ;
    }

    public function scopeDrafted(Builder $builder): Builder
    {
        return $builder
            ->where($this->getPublishableStatus(), PublishStatusEnum::draft)
        ;
    }

    public function scopeShouldBePublished(Builder $builder): Builder
    {
        return $builder
            ->where($this->getPublishableStatus(), PublishStatusEnum::published)
            ->where($this->getPublishablePublishedAt(), '<=', Carbon::now())
        ;
    }

    /**
     * Publish all models where `status` is `scheduled` and `published_at` is less than now.
     */
    public static function publishScheduled(): void
    {
        static::class::all()->each(function (Model $model) {
            if (
                $model->{$model->getPublishableStatus()} === PublishStatusEnum::scheduled
                && $model->{$model->getPublishablePublishedAt()} <= Carbon::now()
            ) {
                $model->publish();
                $model->save();
            }
        });
    }
}
