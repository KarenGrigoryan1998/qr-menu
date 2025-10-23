<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\Restaurant;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationGroup = null;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function getNavigationGroup(): ?string
    {
        return __('filament.nav.restaurant');
    }

    public static function getModelLabel(): string
    {
        return __('filament.models.user');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.models.users');
    }

    public static function canCreate(): bool
    {
        $u = Auth::user();
        return $u && ($u->hasRole('super-admin') || $u->can('create-users'));
    }

    public static function canDeleteAny(): bool
    {
        $u = Auth::user();
        return $u && ($u->hasRole('super-admin') || $u->can('delete-users'));
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')->label(__('filament.fields.name'))->required(),
            TextInput::make('email')->label(__('filament.fields.email'))->email()->required(),
            TextInput::make('password')->label(__('filament.fields.password'))->password()->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)->dehydrated(fn ($state) => filled($state)),
            Select::make('restaurant_id')
                ->label(__('filament.fields.restaurant'))
                ->options(Restaurant::query()->pluck('name', 'id'))
                ->searchable()->preload()->nullable(),
            Select::make('roles')
                ->label(__('filament.fields.roles'))
                ->multiple()
                ->preload()
                ->options(fn () => Role::query()->pluck('name', 'name')->toArray())
                ->afterStateHydrated(function ($component, $state, $record) {
                    if ($record) {
                        $component->state($record->roles->pluck('name')->toArray());
                    }
                })
                ->dehydrateStateUsing(fn ($state) => $state ?? []),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('name')->label(__('filament.fields.name'))->searchable(),
                TextColumn::make('email')->label(__('filament.fields.email'))->searchable(),
                TextColumn::make('restaurant.name')->label(__('filament.fields.restaurant')),
                TextColumn::make('roles.name')->label(__('filament.fields.roles'))->badge(),
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

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();
        if ($user && !$user->hasRole('super-admin')) {
            $query->where('restaurant_id', $user->restaurant_id);
        }
        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
// Page classes are defined in separate files under App\\Filament\\Resources\\UserResource\\Pages
