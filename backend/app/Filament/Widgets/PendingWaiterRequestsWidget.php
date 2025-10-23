<?php

namespace App\Filament\Widgets;

use App\Models\WaiterRequest;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Notifications\Notification;

class PendingWaiterRequestsWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                WaiterRequest::query()
                    ->where('status', 'pending')
                    ->with(['table', 'order'])
                    ->latest()
            )
            ->heading(__('waiter_requests.widgets.pending_requests'))
            ->description(__('waiter_requests.widgets.pending_description'))
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('waiter_requests.fields.id'))
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('table.number')
                    ->label(__('waiter_requests.fields.table'))
                    ->badge()
                    ->color('danger')
                    ->size('lg')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('order.id')
                    ->label(__('waiter_requests.fields.order_id'))
                    ->url(fn ($record) => $record->order_id 
                        ? route('filament.admin.resources.orders.edit', $record->order_id)
                        : null),
                    
                Tables\Columns\TextColumn::make('note')
                    ->label(__('waiter_requests.fields.note'))
                    ->limit(30),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('waiter_requests.fields.created_at'))
                    ->since()
                    ->sortable()
                    ->badge()
                    ->color(fn ($record) => $record->created_at->diffInMinutes(now()) > 5 ? 'danger' : 'warning'),
            ])
            ->actions([
                Tables\Actions\Action::make('acknowledge')
                    ->label(__('waiter_requests.actions.im_on_it'))
                    ->icon('heroicon-o-hand-raised')
                    ->color('success')
                    ->requiresConfirmation(false)
                    ->action(function ($record) {
                        $record->acknowledge();
                        
                        Notification::make()
                            ->title(__('waiter_requests.notifications.acknowledged'))
                            ->success()
                            ->body(__('waiter_requests.notifications.handling', ['table' => $record->table->number]))
                            ->send();
                            
                        // Play sound notification (browser)
                        $this->dispatch('play-notification-sound');
                    }),
            ])
            ->poll('3s') // Refresh every 3 seconds
            ->emptyStateHeading(__('waiter_requests.widgets.empty_heading'))
            ->emptyStateDescription(__('waiter_requests.widgets.empty_description'))
            ->emptyStateIcon('heroicon-o-check-circle');
    }

    public static function canView(): bool
    {
        // Show on dashboard
        return true;
    }
}
