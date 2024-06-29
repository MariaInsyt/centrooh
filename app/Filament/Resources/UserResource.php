<?php

namespace App\Filament\Resources;

use App\Models\User;
use Chiiya\FilamentAccessControl\Contracts\AccessControlUser;
use Chiiya\FilamentAccessControl\Enumerators\Feature;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Filament\Resources\UserResource\Pages\ViewUser;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\Pages\EditUser;
use Carbon\Carbon;
use Chiiya\FilamentAccessControl\Fields\PermissionGroup;
use Chiiya\FilamentAccessControl\Fields\RoleSelect;
use Chiiya\FilamentAccessControl\Traits\HasExtendableSchema;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Livewire\Component;

class UserResource extends Resource
{
    use HasExtendableSchema;
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Administration';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()
                    ->schema(
                        static fn (Component $livewire) => $livewire instanceof ViewUser
                            ? [
                                ...static::insertBeforeFormSchema(),
                                static::detailsSection(),
                                Section::make(__('filament-access-control::default.sections.permissions'))
                                    ->description(__('filament-access-control::default.messages.permissions_view'))
                                    ->schema([
                                        PermissionGroup::make('permissions')
                                            ->label(__('filament-access-control::default.fields.permissions'))
                                            ->validationAttribute(
                                                __('filament-access-control::default.fields.permissions'),
                                            )
                                            ->resolveStateUsing(
                                                static fn ($record) => $record->getAllPermissions()->pluck('id')->all(),
                                            ),
                                    ]),
                                ...static::insertAfterFormSchema(),
                            ] : [
                                ...static::insertBeforeFormSchema(),
                                static::detailsSection(),
                                Section::make(__('filament-access-control::default.sections.permissions'))
                                    ->description(__('filament-access-control::default.messages.permissions_create'))
                                    ->schema([
                                        PermissionGroup::make('permissions')
                                            ->label(__('filament-access-control::default.fields.permissions'))
                                            ->validationAttribute(
                                                __('filament-access-control::default.fields.permissions'),
                                            ),
                                    ]),
                                ...static::insertAfterFormSchema(),
                            ],
                    )
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('role')
                    ->getStateUsing(static fn ($record) => __(optional($record->roles->first())->name)),
                ...(
                    Feature::enabled(Feature::ACCOUNT_EXPIRY)
                    ? [
                        Tables\Columns\IconColumn::make('active')
                            ->boolean()
                            ->getStateUsing(static fn (AccessControlUser $record) => !$record->isExpired()),
                    ]
                    : []
                ),
            ])
            ->actions([EditAction::make(), ViewAction::make()])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ...(
                        Feature::enabled(Feature::ACCOUNT_EXPIRY)
                            ? [
                                BulkAction::make('extend')
                                    ->label(__('filament-access-control::default.actions.extend'))
                                    ->action('extendUsers')
                                    ->requiresConfirmation()
                                    ->deselectRecordsAfterCompletion()
                                    ->color('success')
                                    ->icon('heroicon-o-clock'),
                            ]
                            : []
                    ),
                ]),
            ])
            ->emptyStateActions([CreateAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
            'view' => ViewUser::route('/{record}'),
        ];
    }

    public static function getModel(): string
    {
        return config('filament-access-control.user_model');
    }

    public static function getLabel(): string
    {
        return __('filament-access-control::default.resources.admin_user');
    }

    public static function getPluralLabel(): string
    {
        return __('filament-access-control::default.resources.admin_users');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('roles');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament-access-control::default.resources.group');
    }

    protected static function detailsSectionSchema(): array
    {
        return [
            TextInput::make('name')
                ->label(__('name'))
                ->required(),
            TextInput::make('email')
                ->label(__('filament-access-control::default.fields.email'))
                ->validationAttribute(__('filament-access-control::default.fields.email'))
                ->required()
                ->email(),
            RoleSelect::make('role')
                ->label(__('filament-access-control::default.fields.role'))
                ->validationAttribute(__('filament-access-control::default.fields.role')),
            ...(
                Feature::enabled(Feature::ACCOUNT_EXPIRY)
                ? [
                    DatePicker::make('expires_at')
                        ->label(__('filament-access-control::default.fields.expires_at'))
                        ->validationAttribute(__('filament-access-control::default.fields.expires_at'))
                        ->minDate(static fn (Component $livewire) => static::evaluateMinDate($livewire))
                        ->displayFormat(config('filament-access-control.date_format'))
                        ->dehydrateStateUsing(
                            static fn ($state) => Carbon::parse($state)->endOfDay()->toDateTimeString(),
                        ),
                ]
                : []
            ),
        ];
    }


    protected static function detailsSection(): Section
    {
        return Section::make(__('filament-access-control::default.sections.user_details'))
            ->schema(static::detailsSectionSchema());
    }

    protected static function evaluateMinDate(Component $livewire): ?Carbon
    {
        if ($livewire instanceof CreateUser) {
            return now();
        }

        return null;
    }
}
