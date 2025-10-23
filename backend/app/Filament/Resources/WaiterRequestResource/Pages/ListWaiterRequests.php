<?php

namespace App\Filament\Resources\WaiterRequestResource\Pages;

use App\Filament\Resources\WaiterRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;

class ListWaiterRequests extends ListRecords
{
    protected static string $resource = WaiterRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('acknowledge_all_pending')
                ->label(__('waiter_requests.actions.acknowledge_all'))
                ->icon('heroicon-o-check-badge')
                ->color('warning')
                ->requiresConfirmation()
                ->action(function () {
                    $pending = \App\Models\WaiterRequest::where('status', 'pending')->get();
                    $count = $pending->count();
                    
                    foreach ($pending as $request) {
                        $request->acknowledge();
                    }
                    
                    Notification::make()
                        ->title(__('waiter_requests.notifications.all_acknowledged'))
                        ->success()
                        ->body(__('waiter_requests.notifications.all_acknowledged_body', ['count' => $count]))
                        ->send();
                }),
                
            Actions\Action::make('refresh')
                ->label(__('waiter_requests.actions.refresh'))
                ->icon('heroicon-o-arrow-path')
                ->action(fn () => $this->redirect(static::getUrl())),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            WaiterRequestResource\Widgets\WaiterRequestStats::class,
        ];
    }
}
