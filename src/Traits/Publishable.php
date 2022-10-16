<?php

namespace Kiwilan\Steward\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Kiwilan\Steward\Enums\PublishStatusEnum;
use UnitEnum;

/**
 * Trait Publishable.
 *
 * @property UnitEnum $status can be override by `publishable_status`
 * Cast can be override for all models into config `steward.publishable.enum` or for a specific model into `publishable_status_cast` property, if enum change, update published property with `publishable_status_cast_published` property or `steward.publishable.enum_published`.
 * @property Datetime $published_at can be override by `publishable_published_at`
 *
 * @method void publish() publish the model
 * @method Builder scopePublished(Builder $query) get all models where `status` is `published` and order by `published_at` `desc` works only with native `PublishStatusEnum`
 */
trait Publishable
{
    protected $publishable_status_default = 'status';

    protected $publishable_published_at_default = 'published_at';

    protected $publishable_status_cast_default = PublishStatusEnum::class;

    protected $publishable_status_cast_published_default = 'published';

    public function initializePublishable()
    {
        $this->fillable[] = $this->getPublishableStatus();
        $this->fillable[] = $this->getPublishablePublishedAt();

        $this->casts[$this->getPublishableStatus()] = $this->getPublishableStatusCast();
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

    public function getPublishableStatusCast(): string
    {
        $enum = null;
        if ($config_enum = config('steward.publishable.enum')) {
            $enum = $config_enum;
        }

        if ($this->publishable_status_cast) {
            $enum = $this->publishable_status_cast;
        }

        if (! $enum) {
            $enum = $this->publishable_status_cast_default;
        }

        return $enum;
    }

    public function getPublishableStatusCastPublished(): string
    {
        $enum_published = null;
        if ($config_enum = config('steward.publishable.enum_published')) {
            $enum_published = $config_enum;
        }

        if ($this->publishable_status_cast) {
            $enum_published = $this->publishable_status_cast;
        }

        if (! $enum_published) {
            $enum_published = $this->publishable_status_cast_default;
        }

        return $enum_published;
    }

    public function publish()
    {
        $enum = $this->getPublishableStatusCast()::$this->getPublishableStatusCastPublished();

        $this->{$this->getPublishableStatus()} = $enum;
        $this->{$this->getPublishablePublishedAt()} = Carbon::now();
        $this->save();
    }

    public function scopePublished(Builder $builder)
    {
        $enum = $this->getPublishableStatusCast()::$this->getPublishableStatusCastPublished();

        return $builder
            ->where($this->getPublishableStatus(), $enum)
            ->where($this->getPublishablePublishedAt(), '<=', Carbon::now())
            ->orderBy($this->getPublishablePublishedAt(), 'desc');
    }
}
