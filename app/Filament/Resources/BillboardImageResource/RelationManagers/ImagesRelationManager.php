<?php

namespace App\Filament\Resources\BillboardImageResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\AgentNotification;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;


class ImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('image')
                    ->image()
                    ->downloadable(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('billboard_id')
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->visibility('private')
                    ->width(70)
                    ->height(70)
                    ->square()
                    ->label('Image'),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Active')
                    ->afterStateUpdated(function (Model $record, $state) {
                        $activeImages = $record->active()->where('billboard_id', $record->billboard_id)->get();
                        if (!empty($activeImages)) {
                            foreach ($activeImages as $image) {
                                if ($image->id !== $record->id) {
                                    $image->update(['is_active' => false]);
                                }
                            }
                        }
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->since(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->since(),
            ])->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->headerActions([
                Action::make('requestImage')
                    ->label('Request Billboard Image')
                    ->form([
                        Forms\Components\TextInput::make('title')->required(),
                        Forms\Components\Textarea::make('message')->required(),
                    ])
                    ->action(function (?Model $record, array $data) {
                        Log::info($record);
                    })
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
                Tables\Actions\ViewAction::make(),
                Action::make('sendFeedback')
                    ->label('Send Feedback')
                    ->form([
                        Forms\Components\TextInput::make('title')->required(),
                        Forms\Components\Textarea::make('message')->required(),
                    ])
                    ->action(function (?Model $record, array $data) {
                        Log::info($record);
                    })
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
