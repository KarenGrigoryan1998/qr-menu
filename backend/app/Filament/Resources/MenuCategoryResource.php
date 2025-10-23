<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuCategoryResource\Pages;
use App\Models\MenuCategory;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput\Mask;
use Filament\Forms\Components\TextInput\Masks;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class MenuCategoryResource extends Resource
{
    protected static ?string $model = MenuCategory::class;

    protected static ?string $navigationGroup = null;
    protected static ?string $navigationIcon = 'heroicon-o-tag';

    public static function getNavigationGroup(): ?string
    {
        return __('filament.nav.restaurant');
    }

    public static function getModelLabel(): string
    {
        return __('filament.models.menu_category');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.models.menu_categories');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('filament.fields.general'))->schema([
                    Grid::make(2)->schema([
                        TextInput::make('name')->label(__('filament.fields.default_name'))->required(),
                        Hidden::make('filename'),
                    ]),
                    FileUpload::make('image_path')
                        ->label(__('filament.fields.image'))
                        ->image()
                        ->disk('public')
                        ->directory('menu_categories')
                        ->visibility('public')
                        ->preserveFilenames(false)
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            if ($state instanceof TemporaryUploadedFile) {
                                $original = $state->getClientOriginalName();
                                $set('filename', $original);
                                $currentName = $get('name');
                                if ($currentName === null || $currentName === '') {
                                    $set('name', pathinfo($original, PATHINFO_FILENAME));
                                }
                                return;
                            }

                            if (is_array($state) && isset($state[0]) && $state[0] instanceof TemporaryUploadedFile) {
                                $original = $state[0]->getClientOriginalName();
                                $set('filename', $original);
                                $currentName = $get('name');
                                if ($currentName === null || $currentName === '') {
                                    $set('name', pathinfo($original, PATHINFO_FILENAME));
                                }
                                return;
                            }

                            if (empty($state)) {
                                // File removed
                                $set('filename', null);
                            }
                        })
                        ->imageEditor()
                        ->imageEditorAspectRatios([
                            '1:1',
                            '4:3',
                        ]),
                    TextInput::make('filename')
                        ->label(__('filament.fields.original_filename'))
                        ->disabled()
                        ->dehydrated(false)
                        ->visible(fn (callable $get) => !empty($get('filename'))),
                ]),
                Section::make(__('filament.fields.multilingual'))->schema([
                    Grid::make(3)->schema([
                        TextInput::make('name_hy')->label(__('filament.fields.name').' ('.__('filament.fields.hy').')'),
                        TextInput::make('name_en')->label(__('filament.fields.name').' ('.__('filament.fields.en').')'),
                        TextInput::make('name_ru')->label(__('filament.fields.name').' ('.__('filament.fields.ru').')'),
                    ])
                ])->collapsible(),
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
                    ->circular()
                    ->size(56)
                    ->toggleable(false),
                TextColumn::make('name')->label(__('filament.fields.default_name'))->searchable()->sortable(),
                TextColumn::make('name_hy')->label(__('filament.fields.hy'))->searchable()->sortable(),
                TextColumn::make('name_en')->label(__('filament.fields.en')),
                TextColumn::make('name_ru')->label(__('filament.fields.ru')),
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
            'index' => Pages\ListMenuCategories::route('/'),
            'create' => Pages\CreateMenuCategory::route('/create'),
            'edit' => Pages\EditMenuCategory::route('/{record}/edit'),
        ];
    }
}
