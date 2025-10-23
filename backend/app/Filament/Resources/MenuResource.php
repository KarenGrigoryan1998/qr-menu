<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuResource\Pages;
use App\Models\Menu;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;

    protected static ?string $navigationGroup = null;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationGroup(): ?string
    {
        return __('filament.nav.restaurant');
    }

    public static function getModelLabel(): string
    {
        return __('filament.models.menu');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.models.menus');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('filament.fields.general'))
                    ->schema([
                        Grid::make(2)->schema([
                            Hidden::make('filename'),
                            TextInput::make('name')->label(__('filament.fields.name'))->required()->maxLength(255),
                            Select::make('category_id')
                                ->label(__('filament.fields.category'))
                                ->relationship('category', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                        ]),
                        Grid::make(2)->schema([
                            TextInput::make('price')->label(__('filament.fields.price'))->numeric()->required()->prefix('$'),
                            Toggle::make('available')->label(__('filament.fields.available'))->default(true),
                        ]),
                        Textarea::make('description')->label(__('filament.fields.description'))->rows(3),
                        FileUpload::make('image_path')
                            ->label(__('filament.fields.image'))
                            ->image()
                            ->disk('public')
                            ->directory('menus')
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
                                '1:1',
                                '4:3',
                                '16:9',
                            ]),
                        TextInput::make('filename')
                            ->label(__('filament.fields.original_filename'))
                            ->disabled()
                            ->dehydrated(false)
                            ->visible(fn (callable $get) => !empty($get('filename'))),
                    ])->columns(1),
                Section::make(__('filament.fields.multilingual'))
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('name_hy')->label(__('filament.fields.name').' ('.__('filament.fields.hy').')'),
                            TextInput::make('name_en')->label(__('filament.fields.name').' ('.__('filament.fields.en').')'),
                            TextInput::make('name_ru')->label(__('filament.fields.name').' ('.__('filament.fields.ru').')'),
                        ]),
                        Grid::make(3)->schema([
                            Textarea::make('description_hy')->label(__('filament.fields.description').' ('.__('filament.fields.hy').')')->rows(2),
                            Textarea::make('description_en')->label(__('filament.fields.description').' ('.__('filament.fields.en').')')->rows(2),
                            Textarea::make('description_ru')->label(__('filament.fields.description').' ('.__('filament.fields.ru').')')->rows(2),
                        ]),
                    ])->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable()->toggleable(),
                ImageColumn::make('image_path')
                    ->label(__('filament.fields.image'))
                    ->disk('public')
                    ->circular()
                    ->size(56)
                    ->toggleable(false),
                TextColumn::make('name')->label(__('filament.fields.name'))->searchable()->sortable(),
                TextColumn::make('category.name')->label(__('filament.fields.category'))->sortable(),
                TextColumn::make('price')->label(__('filament.fields.price'))->money('AMD')->sortable(),
                IconColumn::make('available')->boolean(),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label(__('filament.fields.category'))
                    ->relationship('category', 'name'),
                Tables\Filters\TernaryFilter::make('available'),
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
            'index' => Pages\ListMenus::route('/'),
            'create' => Pages\CreateMenu::route('/create'),
            'edit' => Pages\EditMenu::route('/{record}/edit'),
        ];
    }
}
// Page classes are defined in separate files under App\\Filament\\Resources\\MenuResource\\Pages
