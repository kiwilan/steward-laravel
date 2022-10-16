<?php

namespace Kiwilan\Steward\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Kiwilan\Steward\Enums\PublishStatusEnum;
use UnitEnum;

/**
 * Trait Publishable.
 *
 * @property UnitEnum $status       can be override by `publishable_status`
 * @property Datetime $published_at can be override by `publishable_published_at`
 *
 * @method void    publish()                                                  publish the model
 * @method void    unpublish()                                                unpublish the model
 * @method Builder scopePublished(Builder $query, string $direction = 'desc') get all models where `status` is `published` and order by `published_at` `desc`
 */
trait Publishable
{
    protected $publishable_status_default = 'status';

    protected $publishable_published_at_default = 'published_at';

    public function initializePublishable()
    {
        $this->fillable[] = $this->getPublishableStatus();
        $this->fillable[] = $this->getPublishablePublishedAt();

        $this->casts[$this->getPublishableStatus()] = PublishStatusEnum::class;
        $this->casts[$this->getPublishablePublishedAt()] = 'datetime:Y-m-d';
    }

    public function getPublishableStatus(): string
    {
        return $this->publishable_status ?? $this->publishable_status_default;
    }

    public function getPublishablePublishedAt(): string
    {
        return $this->publishable_published_at ?? $this->publishable_published_at_default;
    }

    public function publish()
    {
        $this->{$this->getPublishableStatus()} = PublishStatusEnum::published;
        $this->{$this->getPublishablePublishedAt()} = Carbon::now();
        $this->save();
    }

    public function unpublish()
    {
        $this->{$this->getPublishableStatus()} = PublishStatusEnum::draft;
        $this->{$this->getPublishablePublishedAt()} = null;
        $this->save();
    }

    public function scopePublished(Builder $builder, string $direction = 'desc')
    {
        return $builder
            ->where($this->getPublishableStatus(), PublishStatusEnum::published)
            ->where($this->getPublishablePublishedAt(), '<=', Carbon::now())
            ->orderBy($this->getPublishablePublishedAt(), $direction);
    }
}
