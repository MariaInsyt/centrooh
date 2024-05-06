<?php

namespace App\Filament\Resources\BillboardResource\Pages;

use App\Filament\Resources\BillboardResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use App\Models\Agent;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use App\Models\AgentNotification;
use App\Notifications\DeviceAgentNotification;
use App\Models\Device;

class EditBillboard extends EditRecord
{
    protected static string $resource = BillboardResource::class;
    
    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Action::make('requestImage')
            ->label('Request Billboard Image')
            ->icon('heroicon-o-camera')
            ->action(
                function () {
                    $agent = Agent::find($this->record->agent_id);

                    if (is_null($agent)) {
                        Log::error('Agent not found for billboard: ' . $this->record->id);
                        return;
                    }
                    try {
                        $notification = new AgentNotification();
                        $notification->agent_id = $agent->id;
                        $notification->title = 'Image Request';
                        $notification->message = 'Please provide an image for billboard: ' . $this->record->name . ' (' . $this->record->address . ')';
                        $notification->save();
                    } catch (\Exception $e) {
                        Log::error('Error sending notification: ' . $e->getMessage());
                    }

                    $device = Device::where('agent_id', $agent->id)->first();

                    if (is_null($device)) {
                        Log::error('Device not found for agent: ' . $agent->id);
                        return;
                    }

                    try {
                        $device->notify(new DeviceAgentNotification('Image Request', 'Please provide an image for billboard: ' . $this->record->name . ' (' . $this->record->address . ')'));
                    } catch (\Exception $e) {
                        Log::error('Error sending notification: ' . $e->getMessage());
                    }
                }
            )
            ->requiresConfirmation()
            ->modalHeading('Request Billboard Image')
            ->modalDescription('This will dispatch a notification to the agent to request an image for this billboard.')
            ->modalSubmitActionLabel('Send Request'),
            // Actions\DeleteAction::make(),
            // Actions\ForceDeleteAction::make(),
            // Actions\RestoreAction::make(),
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
