<?php

namespace App\Filament\Resources\AgentDistrictResource\Pages;

use App\Filament\Resources\AgentDistrictResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAgentDistricts extends ListRecords
{
    protected static string $resource = AgentDistrictResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
