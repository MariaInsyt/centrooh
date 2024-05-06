<?php

namespace App\Filament\Resources\BillboardResource\Pages;

use App\Filament\Resources\BillboardResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Log;
use App\Models\Agent;
use App\Models\AgentNotification;
use App\Notifications\DeviceAgentNotification;
use App\Models\Device;

class ViewBillboard extends ViewRecord
{
    protected static string $resource = BillboardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Action::make('requestImage')
                ->label('Request Image')
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
        ];
    }
}
