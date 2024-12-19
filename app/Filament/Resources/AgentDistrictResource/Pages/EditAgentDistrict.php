<?php

namespace App\Filament\Resources\AgentDistrictResource\Pages;

use App\Filament\Resources\AgentDistrictResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAgentDistrict extends EditRecord
{
    protected static string $resource = AgentDistrictResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
