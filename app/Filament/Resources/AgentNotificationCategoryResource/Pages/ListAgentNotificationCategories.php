<?php

namespace App\Filament\Resources\AgentNotificationCategoryResource\Pages;

use App\Filament\Resources\AgentNotificationCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAgentNotificationCategories extends ListRecords
{
    protected static string $resource = AgentNotificationCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
