<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Chiiya\FilamentAccessControl\Notifications\SetPassword;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;

class CreateUser extends CreateRecord
{
    public static function getResource(): string
    {
        return config('filament-access-control.resources.user', UserResource::class);
    }

    public function afterCreate(): void
    {
        $user = $this->record;
        $token = Password::broker('filament')->createToken($user);
        $url = Filament::getResetPasswordUrl($token, $user);
        $requestUrl = Filament::getRequestPasswordResetUrl();
        $user->notify(new SetPassword($url, $requestUrl));

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['password'] = Str::random(40);

        return $data;
    }
}
