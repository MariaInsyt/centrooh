<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AgentResource\Pages;
use App\Filament\Resources\AgentResource\RelationManagers\AgentDistrictsRelationManager;
use App\Filament\Resources\AgentResource\RelationManagers\BillboardsRelationManager;
use App\Models\Agent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use App\Http\Controllers\AgentController;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;
use App\Notifications\AccountActivated;
use Filament\Notifications\Notification;
use App\Models\Device;
use Illuminate\Support\Facades\Log;

class AgentResource extends Resource
{
    protected static ?string $model = Agent::class;

    protected static ?string $navigationIcon = 'heroicon-s-user-group';

    protected static ?string $navigationGroup = 'Operations';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Personal Information')
                    ->collapsible()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->regex('/^[a-zA-Z\s]*$/')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, string $state, Forms\Set $set) {
                                if ($operation === 'create') {
                                    $set('username', AgentController::createUserName($state));
                                } else {
                                    return;
                                }
                            })
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('username')
                            ->readOnly()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(255)
                            // ->unique()
                            ->required(),
                        Forms\Components\TextInput::make('phone_number')
                            ->tel()
                            ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                            ->startsWith('+256')
                            // ->unique()
                            ->hint('Begin +256..')
                            ->required(),
                    ])->columnSpan(2)->columns(2),
                Section::make('Meta')->schema([
                    Forms\Components\Toggle::make('status')
                        ->default(false)
                        ->afterStateUpdated(function (?Model $record, string $operation, bool $state, Forms\Set $set) {
                            if ($operation === 'create') {
                                $set('status', false);
                                return;
                            } else if ($operation === 'edit') {
                                if (
                                    $record->devices->count() > 0
                                ) {
                                    if ($state === true) {
                                        $device = Device::where([[
                                            'agent_id', $record->id,
                                            ['is_active', true]
                                        ]])->first();
                                        try {
                                            $device->notify(new AccountActivated);
                                        } catch (\Exception $e) {
                                            Log::error($e->getMessage());
                                        }
                                    }
                                }
                                Notification::make()
                                    ->title('No Devices')
                                    ->body('Agent must sign in with at least one device before they can be activated.')
                                    ->warning()
                                    ->persistent()
                                    ->send();
                                $set('status', false);
                                return;
                            }
                        }),
                    Forms\Components\FileUpload::make('profile_picture')
                        ->disk('do')
                        ->directory('profilepictures')
                        ->image()
                        ->imageEditor()
                        ->imageEditorAspectRatios([
                            null,
                            '16:9',
                            '4:3',
                            '1:1',
                        ]),
                ])->columnSpan(1),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('profile_picture')
                    ->defaultImageUrl(url('https://placehold.co/600x400'))
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('username')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone_number')
                    ->searchable(),
                Tables\Columns\IconColumn::make('status')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->since(),
            ])->defaultSort('created_at', 'desc')
            ->filters([
                //
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])->poll('60s');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
            AgentDistrictsRelationManager::class,
            BillboardsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAgents::route('/'),
            'create' => Pages\CreateAgent::route('/create'),
            'view' => Pages\ViewAgent::route('/{record}'), // '/{record}
            'edit' => Pages\EditAgent::route('/{record}/edit'),
        ];
    }
}
