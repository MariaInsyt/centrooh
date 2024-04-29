<?php

namespace App\Filament\Resources\AgentNotificationCategoryResource\Pages;

use App\Filament\Resources\AgentNotificationCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAgentNotificationCategory extends EditRecord
{
    protected static string $resource = AgentNotificationCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
