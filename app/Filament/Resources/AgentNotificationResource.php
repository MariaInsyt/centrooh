<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AgentNotificationResource\Pages;
use App\Filament\Resources\AgentNotificationResource\RelationManagers;
use App\Models\Agent;
use App\Models\AgentNotification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AgentNotificationResource extends Resource
{
    protected static ?string $model = AgentNotification::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell-alert';

    protected static ?string $navigationGroup = 'Operations';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Forms\Components\Select::make('agent_id')
                //     ->searchable()
                //     ->relationship('agent', 'name')
                //     ->preload()
                //     ->required(),
                Forms\Components\Select::make('agent_id')
                ->label('Agent')
                ->searchable()
                ->getSearchResultsUsing(
                    fn (string $search) => Agent::active()
                    ->where('name', 'like', '%' . $search . '%')
                    ->orWhere('phone_number', 'like', '%' . $search . '%')
                    ->limit(10)->pluck('name', 'id')->toArray()
                )
                ->getOptionLabelUsing(fn ($value) => 
                Agent::find($value)->name . ' - ' . Agent::find($value)->phone_number
                )
                ->helperText('Search for active agents with name or phone number(256...).'),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('message')
                    ->required()
                    ->columnSpanFull(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('agent.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Content')
                    ->description(
                        fn (AgentNotification $record): string => $record->message
                    )
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Sent by')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('read_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAgentNotifications::route('/'),
            'create' => Pages\CreateAgentNotification::route('/create'),
            'edit' => Pages\EditAgentNotification::route('/{record}/edit'),
        ];
    }
}
