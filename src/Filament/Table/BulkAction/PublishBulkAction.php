<?php

namespace Kiwilan\Steward\Filament\Table\BulkAction;

use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class PublishBulkAction extends BulkAction
{
    use CanCustomizeProcess;

    public static function getDefaultName(): ?string
    {
        return 'publish';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('steward::filament.actions.publish_button'));
        $this->color('primary');
        $this->icon('heroicon-o-paper-airplane');
        $this->outlined();

        $this->successNotificationTitle(__('steward::filament.actions.publish_notification_success'));

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

                $records->each(fn (Model $record) => method_exists($record, 'publish') ? $record->publish() : null);
            });

            $this->success();
        });

        $this->deselectRecordsAfterCompletion();
    }
}
