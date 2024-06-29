<?php 

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Collection;

class ListUsers extends ListRecords
{
    public static function getResource(): string
    {
        return config('filament-access-control.resources.user', UserResource::class);
    }

    public function extendUsers(Collection $users): void
    {
        $users->each->extend();

        Notification::make()->title(
            __('filament-access-control::default.messages.accounts_extended'),
        )->success()->send();
    }

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
