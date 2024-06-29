<?php 

namespace App\Filament\Resources\PermissionResource\Pages;

use App\Filament\Resources\PermissionResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePermission extends CreateRecord
{
    public static function getResource(): string
    {
        return config('filament-access-control.resources.permission', PermissionResource::class);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['guard_name'] = 'filament';

        return $data;
    }
}
