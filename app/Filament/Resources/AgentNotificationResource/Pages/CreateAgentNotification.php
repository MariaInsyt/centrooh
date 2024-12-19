<?php

namespace App\Filament\Resources\AgentNotificationResource\Pages;

use App\Filament\Resources\AgentNotificationResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Device;
use App\Notifications\DeviceAgentNotification;
use Illuminate\Support\Facades\Log;

class CreateAgentNotification extends CreateRecord
{
    protected static string $resource = AgentNotificationResource::class;


    protected function afterCreate(): void
    {
        $device = Device::where([
            ['agent_id', $this->data['agent_id']],
            ['is_active', true]
        ])->first();

        try {
            $device->notify(new DeviceAgentNotification(
                title: $this->data['title'],
                body: $this->data['message']
            ));
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
