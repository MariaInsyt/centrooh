<?php

namespace App\Filament\Resources\BillboardImageResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';

    public function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('billboard_id')
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->defaultImageUrl(url('https://placehold.co/600x400'))
                    ->visibility('private')
                    ->width(70)
                    ->height(70)
                    ->square()
                    ->label('Image'),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Active'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->since(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->since(),
            ])->defaultSort('created_at', 'desc')
            ->poll(120)
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
