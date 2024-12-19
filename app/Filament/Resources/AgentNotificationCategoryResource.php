<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AgentNotificationCategoryResource\Pages;
use App\Filament\Resources\AgentNotificationCategoryResource\RelationManagers;
use App\Models\AgentNotificationCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AgentNotificationCategoryResource extends Resource
{
    protected static ?string $model = AgentNotificationCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-bars-3-bottom-left';

    protected static ?string $navigationLabel = 'Notification Categories';

    protected static ?string $navigationGroup = 'Configuration';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_active')
                    ->default(true)
                    ->required(),
                Forms\Components\Textarea::make('message_template')
                    ->columnSpanFull(),
                Forms\Components\ColorPicker::make('color'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\ColorColumn::make('color'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                // Tables\Columns\TextColumn::make('icon')
                //     ->searchable(),
                // Tables\Columns\TextColumn::make('created_by')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('updated_by')
                //     ->numeric()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('deleted_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                // Tables\Columns\TextColumn::make('created_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                // Tables\Columns\TextColumn::make('updated_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
            ])
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
            'index' => Pages\ListAgentNotificationCategories::route('/'),
            'create' => Pages\CreateAgentNotificationCategory::route('/create'),
            'edit' => Pages\EditAgentNotificationCategory::route('/{record}/edit'),
        ];
    }
}
