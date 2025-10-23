<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentOrdersWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Order::query()
                    ->with(['table', 'restaurant'])
                    ->latest()
                    ->limit(10)
            )
            ->heading(__('filament.models.orders'))
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('ID'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('table.number')
                    ->label(__('filament.fields.table'))
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label(__('filament.fields.status'))
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'paid',
                        'info' => 'cooking',
                        'primary' => 'ready',
                        'gray' => 'delivered',
                        'secondary' => 'closed',
                    ]),

                Tables\Columns\BadgeColumn::make('payment_status')
                    ->label(__('filament.fields.payment'))
                    ->colors([
                        'danger' => 'unpaid',
                        'success' => 'paid',
                        'warning' => 'refunded',
                    ]),

                Tables\Columns\TextColumn::make('total_amount')
                    ->label(__('filament.fields.price'))
                    ->money('AMD')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament.fields.time') ?? 'Time')
                    ->since()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('')
                    ->tooltip(__('filament.actions.view') ?? 'View')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => route('filament.admin.resources.orders.edit', $record)),
            ])
            ->poll('10s');
    }
}
