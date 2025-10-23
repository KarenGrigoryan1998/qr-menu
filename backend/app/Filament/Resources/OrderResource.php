<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers\ItemsRelationManager;
use App\Models\Order;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationGroup = null;
    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';

    public static function getModelLabel(): string
    {
        return __('filament.models.order');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.models.orders');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.nav.restaurant');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('status')
                ->label(__('filament.fields.status'))
                ->options([
                    'pending' => __('filament.statuses.pending'),
                    'paid' => __('filament.statuses.paid'),
                    'cooking' => __('filament.statuses.cooking'),
                    'ready' => __('filament.statuses.ready'),
                    'delivered' => __('filament.statuses.delivered'),
                    'closed' => __('filament.statuses.closed'),
                ])->required(),
            Select::make('payment_status')
                ->label(__('filament.fields.payment'))
                ->options([
                    'unpaid' => __('filament.statuses.unpaid'),
                    'paid' => __('filament.statuses.paid'),
                    'refunded' => __('filament.statuses.refunded'),
                ])->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('table.number')->label(__('filament.fields.table'))->sortable(),
                BadgeColumn::make('status')
                    ->label(__('filament.fields.status'))
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'paid',
                        'info' => 'cooking',
                        'primary' => 'ready',
                        'gray' => 'delivered',
                        'secondary' => 'closed',
                    ])
                    ->formatStateUsing(fn ($state) => __('filament.statuses.' . $state)),
                BadgeColumn::make('payment_status')
                    ->label(__('filament.fields.payment'))
                    ->colors([
                        'danger' => 'unpaid',
                        'success' => 'paid',
                        'warning' => 'refunded',
                    ])
                    ->formatStateUsing(fn ($state) => __('filament.statuses.' . $state)),
                TextColumn::make('total_amount')->label(__('filament.fields.price'))->money('AMD')->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('advance')
                    ->label(__('filament.actions.advance_status'))
                    ->action(function (Order $record) {
                        $flow = ['pending','cooking','ready','delivered','closed'];
                        $idx = array_search($record->status, $flow, true);
                        if ($idx !== false && $idx < count($flow) - 1) {
                            $record->status = $flow[$idx + 1];
                            $record->save();
                        }
                    })
                    ->visible(fn (Order $record) => in_array($record->status, ['pending','cooking','ready','delivered'])),
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

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
        ];
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
            'index' => Pages\ListOrders::route('/'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}

namespace App\Filament\Resources\OrderResource\RelationManagers;

use App\Filament\Resources\OrderResource\Pages\CreateOrder;
use App\Filament\Resources\OrderResource\Pages\EditOrder;
use App\Filament\Resources\OrderResource\Pages\ListOrders;
use App\Models\OrderItem;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('menu_id')->disabled(),
            TextInput::make('quantity')->numeric()->required(),
            TextInput::make('price')->numeric()->required(),
            TextInput::make('comment'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('menu.name')->label(__('filament.models.item')),
            TextColumn::make('quantity')->label(__('filament.fields.quantity')),
            TextColumn::make('price')->money('AMD')->label(__('filament.fields.price')),
            TextColumn::make('comment')->label(__('filament.fields.comment')),
        ]);
    }
    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            'create' => CreateOrder::route(__('filament.actions.create') . ' ' . __('filament.models.order')),
        ];
    }
}

// Page classes are defined in separate files under App\\Filament\\Resources\\OrderResource\\Pages
