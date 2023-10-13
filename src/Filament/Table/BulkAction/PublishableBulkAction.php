<?php

namespace Kiwilan\Steward\Filament\Table\BulkAction;

use Filament\Tables\Actions\BulkActionGroup;

class PublishableBulkAction
{
    public static function make(): BulkActionGroup
    {
        return BulkActionGroup::make([
            PublishBulkAction::make(),
            UnpublishBulkAction::make(),
        ])->label(__('steward::filament.actions.publish_bulk_group'));
    }
}
