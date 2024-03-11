<?php

namespace App\Filament\Resources\BillboardResource\Pages;

use App\Filament\Resources\BillboardResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\Agent;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class EditBillboard extends EditRecord
{
    protected static string $resource = BillboardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if (array_key_exists('agent_id', $data)) {
            $agentDistricts = Agent::find($data['agent_id'])->agentDistricts->pluck('district_id')->toArray();

            if (array_key_exists('district_id', $data)) {
                if (!in_array($data['district_id'], $agentDistricts)) {
                    Notification::make()
                        ->warning()
                        ->title('Hold up!')
                        ->body('The selected agent has not been assigned to the selected district of this billboard. Please select another agent.')
                        ->persistent()
                        ->send();

                    $this->halt();
                }
            }
        }
        $record->update($data);

        return $record;
    }
}
