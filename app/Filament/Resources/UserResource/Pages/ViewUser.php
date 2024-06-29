<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\ViewRecord;

class ViewUser extends ViewRecord
{
    public static function getResource(): string
    {
        return config('filament-access-control.resources.user', UserResource::class);
    }
}
