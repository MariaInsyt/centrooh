<?php

namespace App\Filament\Resources\BillboardResource\Pages;

use App\Filament\Resources\BillboardResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\Agent;
use Filament\Notifications\Notification;

class EditBillboard extends EditRecord
{
    protected static string $resource = BillboardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function beforeSave(): void
    {
        $agent = Agent::find($this->record->agent_id);
        $agent_districts = $agent->agentDistricts->pluck('district_id')->toArray();
        $billboard_district = $this->record->district_id;

        if (!in_array($billboard_district, $agent_districts)) {
            Notification::make()
                ->danger()
                ->title('Hold up!')
                ->body('Agent has not been assigned to the district of this billboard. Please assign the agent to the district of this billboard.')
                ->persistent()
                ->send();
            $this->halt();
        }
    }
}
