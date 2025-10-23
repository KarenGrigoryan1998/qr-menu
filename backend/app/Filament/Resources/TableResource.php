<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TableResource\Pages;
use App\Models\Table as DiningTable;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\View as FormView;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\Services\QrGenerator;
use Filament\Notifications\Notification;

class TableResource extends Resource
{
    protected static ?string $model = DiningTable::class;

    protected static ?string $navigationGroup = null;
    protected static ?string $navigationIcon = 'heroicon-o-qr-code';

    public static function getNavigationGroup(): ?string
    {
        return __('filament.nav.restaurant');
    }

    public static function getModelLabel(): string
    {
        return __('filament.models.table');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.models.tables');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('filament.fields.table').' '.__('filament.fields.general'))->schema([
                    Grid::make(3)->schema([
                        TextInput::make('number')->label('#')->numeric()->required(),
                        Select::make('status')
                            ->label(__('filament.fields.status'))
                            ->options([
                                'free' => __('filament.statuses.free'),
                                'occupied' => __('filament.statuses.occupied'),
                                'reserved' => __('filament.statuses.reserved'),
                            ])->required(),
                        TextInput::make('qr_code_url')
                            ->label(__('filament.fields.qr_url'))
                            ->suffixAction(
                                FormAction::make('generate')
                                    ->icon('heroicon-m-qr-code')
                                    ->tooltip(__('Generate QR URL'))
                                    ->action(function ($livewire) {
                                        $record = $livewire->getRecord();
                                        $front = config('app.frontend_url') ?? env('FRONTEND_URL', config('app.url'));
                                        $restaurantId = $record->restaurant_id ?? Auth::user()?->restaurant_id;
                                        if ($restaurantId && $record?->id) {
                                            $record->qr_code_url = rtrim($front, '/') . "/r/{$restaurantId}/t/{$record->id}";
                                            $record->save();
                                            Notification::make()
                                                ->title(__('filament.notifications.qr_generated'))
                                                ->success()
                                                ->send();
                                        }
                                    })
                            )
                            ->disabled()
                            ->dehydrated(false),
                        Select::make('qr_code_size')
                            ->label(__('filament.fields.size') ?? 'Size')
                            ->options([
                                256 => '256px',
                                512 => '512px',
                                1024 => '1024px',
                            ])
                            ->default(1024),
                    ]),
                ]),
                Section::make('')
                    ->schema([
                        FormView::make('filament.components.qr-preview')
                            ->visible(fn ($record) => filled($record?->qr_code_url))
                            ->viewData(fn ($record) => [
                                'qr_thumb_url' => app(\App\Services\QrGenerator::class)->getThumbUrl($record),
                                'qr_download_url' => app(\App\Services\QrGenerator::class)->getDownloadUrl($record),
                                'qr_table_url' => $record?->qr_code_url,
                            ]),
                    ]),
            ]);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('number')->label('#')->sortable(),
                BadgeColumn::make('status')
                    ->label(__('filament.fields.status'))
                    ->colors([
                        'success' => 'free',
                        'warning' => 'reserved',
                        'danger' => 'occupied',
                    ])
                    ->formatStateUsing(fn ($state) => __('filament.statuses.' . $state)),
                ImageColumn::make('qr_code_url')
                    ->label(__('filament.fields.qr_url'))
                    ->getStateUsing(fn ($record) => app(QrGenerator::class)->getThumbUrl($record))
                    ->size(56)
                    ->extraImgAttributes(fn ($record) => [
                        'alt' => 'QR',
                        'title' => $record->qr_code_url,
                    ])
                    ->url(fn ($record) => $record->qr_code_url)
                    ->openUrlInNewTab(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('')
                    ->tooltip(__('filament.actions.edit'))
                    ->icon('heroicon-o-pencil-square'),
                Action::make('generate_qr')
                    ->label('')
                    ->tooltip(__('filament.actions.generate_qr_url'))
                    ->icon('heroicon-m-qr-code')
                    ->form([
                        Select::make('size')
                            ->label(__('filament.fields.size') ?? 'Size')
                            ->options([
                                256 => '256px',
                                512 => '512px',
                                1024 => '1024px',
                            ])
                            ->default(fn (DiningTable $record) => $record->qr_code_size ?? 1024)
                            ->required(),
                    ])
                    ->action(function (DiningTable $record, array $data) {
                        $front = config('app.frontend_url') ?? env('FRONTEND_URL', config('app.url'));
                        $record->qr_code_url = rtrim($front, '/') . "/r/{$record->restaurant_id}/t/{$record->id}";
                        // Persist selected size
                        $record->qr_code_size = (int)($data['size'] ?? 1024);
                        $record->save();
                        // Generate local images (thumbnail and selected size)
                        $service = app(QrGenerator::class);
                        $service->generateForTable($record, 256);
                        $path = $service->generateForTable($record, $record->qr_code_size ?? 1024);
                        if ($path) {
                            $record->qr_code_filename = $path;
                            $record->save();
                        }
                    }),
                Action::make('download_qr')
                    ->label('')
                    ->tooltip(__('filament.actions.download_qr') ?? 'Download QR')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->url(fn (DiningTable $record) => app(QrGenerator::class)->getDownloadUrl($record) ?? null)
                    ->openUrlInNewTab()
                    ->extraAttributes(fn (DiningTable $record) => [
                        'download' => "table-{$record->id}-qr.png",
                    ]),
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
            'index' => Pages\ListTables::route('/'),
            'create' => Pages\CreateTable::route('/create'),
            'edit' => Pages\EditTable::route('/{record}/edit'),
        ];
    }
}
