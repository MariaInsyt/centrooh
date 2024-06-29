<?php 

namespace App\Filament\Resources\PermissionResource\Pages;

use App\Filament\Resources\PermissionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPermission extends EditRecord
{
    public static function getResource(): string
    {
        return config('filament-access-control.resources.permission', PermissionResource::class);
    }

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
