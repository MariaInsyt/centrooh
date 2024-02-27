<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BillboardImageResource\RelationManagers\ImagesRelationManager;
use App\Filament\Resources\BillboardResource\Pages;
use App\Filament\Resources\BillboardResource\RelationManagers;
use App\Models\Billboard;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Cheesegrits\FilamentGoogleMaps\Fields\Map;
use Cheesegrits\FilamentGoogleMaps\Fields\Geocomplete;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BillboardResource extends Resource
{
    protected static ?string $model = Billboard::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Operations';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'updated' => 'Updated',
                        'notupdated' => 'Not Updated',
                        'rejected' => 'rejected'
                    ])
                    ->required(),
                Forms\Components\Select::make('district_id')
                    ->relationship('district', 'name')
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('agent_id')
                    ->relationship('agent', 'name')
                    ->searchable(),
                Forms\Components\Toggle::make('is_active')
                    ->default(true)
                    ->required(),
                Map::make('map')
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        $set('lat', $state['lat']);
                        $set('lng', $state['lng']);
                    })
                    ->mapControls([
                        'mapTypeControl'    => false,
                        'scaleControl'      => true,
                        'streetViewControl' => false,
                        'rotateControl'     => false,
                        'fullscreenControl' => true,
                        'searchBoxControl'  => false,
                        'zoomControl'       => true,
                    ])
                    ->height(fn () => '400px')
                    ->defaultZoom(15)
                    ->defaultLocation(fn ($record) => [
                        $record->lat ?? 0.3401327,
                        $record->lng ?? 32.5864384,
                    ])
                    ->draggable()
                    ->clickable(false)
                    ->geolocate() // adds a button to request device location and set map marker accordingly
                    ->geolocateOnLoad(true, false)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('lat')
                    ->label('Latitude')
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        $set('map', [
                            'lat' => floatVal($state),
                            'lng' => floatVal($get('longitude')),
                        ]);
                    })
                    ->lazy(), // important to use lazy, to avoid updates as you type
                Forms\Components\TextInput::make('lng')
                    ->label('Longitude')
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        $set('map', [
                            'lat' => floatval($get('latitude')),
                            'lng' => floatVal($state),
                        ]);
                    })
                    ->lazy(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'updated' => 'success',
                        'notupdated' => 'warning',
                        'rejected' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('district.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('agent.name'),
                Tables\Columns\TextColumn::make('createdBy.name')
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
                    ->label('Date Updated')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
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
            ImagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBillboards::route('/'),
            'create' => Pages\CreateBillboard::route('/create'),
            'edit' => Pages\EditBillboard::route('/{record}/edit'),
        ];
    }
}
