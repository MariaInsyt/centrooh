<?php

namespace App\Filament\Resources\AgentResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AgentDistrictsRelationManager extends RelationManager
{
    protected static string $relationship = 'agentDistricts';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('agent_id')
                    ->searchable()
                    ->relationship('agent', 'name')
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('district_id')
                    ->searchable()
                    ->relationship('district', 'name')
                    ->required(),
                Forms\Components\Toggle::make('is_active')
                    ->required(),
                // Forms\Components\Toggle::make('is_primary')
                    // ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('district_id')
            ->columns([
                // Tables\Columns\TextColumn::make('agent.name'),
                Tables\Columns\TextColumn::make('district.name'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                // Tables\Columns\IconColumn::make('is_primary')
                    // ->boolean(),
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
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
