<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RestaurantResource\Pages;
use App\Models\Restaurant;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\View as FormView;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Form;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;

class RestaurantResource extends Resource
{
    protected static ?string $model = Restaurant::class;

    protected static ?string $navigationGroup = null;
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    public static function getNavigationGroup(): ?string
    {
        return __('filament.nav.restaurant');
    }

    public static function getModelLabel(): string
    {
        return __('filament.models.restaurant');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.models.restaurants');
    }



    public static function form(Form $form): Form
    {
        return $form->schema([
            Hidden::make('filename'),
            TextInput::make('name')
                ->label(__('filament.fields.name'))
                ->required()
                ->maxLength(255),
            TextInput::make('slug')
                ->label(__('filament.fields.slug'))
                ->required()
                ->unique(ignoreRecord: true),
            // FileUpload stores to storage/app/public/restaurants/ (via 'public' disk)
            // Benefit: Laravel manages visibility, can easily switch to S3/cloud storage later
            // Requires: php artisan storage:link to create public/storage symlink
            FileUpload::make('image_path')
                ->label(__('filament.fields.header_image'))
                ->image()
                ->disk('public')
                ->directory('restaurants')
                ->visibility('public')
                ->preserveFilenames(false)
                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                    if ($state instanceof TemporaryUploadedFile) {
                        $original = $state->getClientOriginalName();
                        $set('filename', $original);
                        return;
                    }

                    if (is_array($state) && isset($state[0]) && $state[0] instanceof TemporaryUploadedFile) {
                        $original = $state[0]->getClientOriginalName();
                        $set('filename', $original);
                        return;
                    }

                    if (empty($state)) {
                        $set('filename', null);
                    }
                })
                ->imageEditor()
                ->imageEditorAspectRatios([
                    '16:9',
                    '4:3',
                ]),
            TextInput::make('filename')
                ->label(__('filament.fields.original_filename'))
                ->disabled()
                ->dehydrated(false)
                ->visible(fn (callable $get) => !empty($get('filename'))),
            Forms\Components\KeyValue::make('settings')
                ->keyLabel(__('filament.fields.key'))
                ->valueLabel(__('filament.fields.value'))
                ->afterStateHydrated(function ($component, $state) {
                    // Ensure all values are strings for the KeyValue component
                    if (is_array($state)) {
                        $component->state(collect($state)->map(
                            fn ($v) => is_array($v) ? json_encode($v) : (string) $v
                        )->toArray());
                    }
                })
                ->dehydrateStateUsing(function ($state) {
                    // Persist as array of strings (encode nested arrays)
                    if (! is_array($state)) {
                        return $state;
                    }
                    return collect($state)->map(
                        fn ($v) => is_array($v) ? json_encode($v) : (string) $v
                    )->toArray();
                })
                ->addButtonLabel(__('filament.fields.add_setting')),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                ImageColumn::make('image_path')
                    ->label(__('filament.fields.image'))
                    ->disk('public')
                    ->square(),
                TextColumn::make('name')->label(__('filament.fields.name'))->searchable()->sortable(),
                TextColumn::make('slug')->label('Slug')->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('')
                    ->tooltip(__('filament.actions.edit'))
                    ->icon('heroicon-o-pencil-square'),
                Tables\Actions\DeleteAction::make()
                    ->label('')
                    ->tooltip(__('filament.actions.delete'))
                    ->icon('heroicon-o-trash'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRestaurants::route('/'),
            'create' => Pages\CreateRestaurant::route('/create'),
            'edit' => Pages\EditRestaurant::route('/{record}/edit'),
        ];
    }
}
