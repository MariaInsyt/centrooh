<?php

namespace App\Filament\Resources\BillboardImageResource\RelationManagers;

use App\Models\Billboard;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Notifications\DeviceAgentNotification;
use App\Models\AgentNotification;
use App\Models\Device;

class ImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';

    public function isReadOnly(): bool
    {
        return false;
    }

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
            ->modifyQueryUsing(fn (Builder $query) => $query->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]))
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
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Action::make('sendFeedback')
                        ->icon('heroicon-o-paper-airplane')
                        ->label('Send Feedback')
                        ->form([
                            Forms\Components\TextInput::make('title')
                                ->default('Feedback on Image')
                                ->required(),
                            Forms\Components\Textarea::make('message')->required(),
                        ])
                        ->action(function (?Model $record, array $data) {
                            $billboard = Billboard::find($record->billboard_id);

                            if (is_null($billboard->agent_id)) {
                                return;
                            }
                            $device = Device::where('agent_id', $billboard->agent_id)->first();

                            try {
                                AgentNotification::create([
                                    'agent_id' => $billboard->agent_id,
                                    'title' => $data['title'],
                                    'message' => $data['message'],
                                ]);

                                $device->notify(new DeviceAgentNotification($data['title'], $data['message']));

                            } catch (\Exception $e) {
                                Log::error($e->getMessage());
                            }
                        }),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\ForceDeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                ])
                    ->button()
                    ->label('Actions')
            ])
            ->bulkActions([]);
    }
}
