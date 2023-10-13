<?php

namespace Kiwilan\Steward\Filament\Table\BulkAction;

use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class UnpublishBulkAction extends BulkAction
{
    use CanCustomizeProcess;

    public static function getDefaultName(): ?string
    {
        return 'unpublish';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('steward::filament.actions.unpublish_button'));
        $this->color('danger');
        $this->icon('heroicon-o-archive-box');
        $this->outlined();

        $this->successNotificationTitle(__('steward::filament.actions.unpublish_notification_success'));

        $this->action(function (): void {
            $this->process(static function (Collection $records) {
                $model = $records->first();
                $class = get_class($model);
                $trait = 'Kiwilan\Steward\Traits\Publishable';
                $traits = class_uses_recursive($class);
                $isPublishable = in_array($trait, $traits);

                if (! $isPublishable) {
                    throw new \Exception("{$class} is not publishable");
                }

                $records->each(fn (Model $record) => method_exists($record, 'unpublish') ? $record->unpublish() : null);
            });

            $this->success();
        });

        $this->deselectRecordsAfterCompletion();
    }
}
