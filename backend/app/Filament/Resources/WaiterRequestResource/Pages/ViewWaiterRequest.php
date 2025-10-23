<?php

namespace App\Filament\Resources\WaiterRequestResource\Pages;

use App\Filament\Resources\WaiterRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewWaiterRequest extends ViewRecord
{
    protected static string $resource = WaiterRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('waiter_requests.sections.request_details'))
                    ->schema([
                        Infolists\Components\TextEntry::make('id')
                            ->label(__('waiter_requests.fields.id')),
                        Infolists\Components\TextEntry::make('status')
                            ->label(__('waiter_requests.fields.status'))
                            ->badge()
                            ->color(fn ($state) => match($state) {
                                'pending' => 'danger',
                                'acknowledged' => 'warning',
                                'completed' => 'success',
                            }),
                        Infolists\Components\TextEntry::make('table.number')
                            ->label(__('waiter_requests.fields.table_number')),
                        Infolists\Components\TextEntry::make('order.id')
                            ->label(__('waiter_requests.fields.order_id'))
                            ->url(fn ($record) => $record->order_id 
                                ? route('filament.admin.resources.orders.edit', $record->order_id)
                                : null),
                    ])
                    ->columns(2),
                    
                Infolists\Components\Section::make(__('waiter_requests.sections.timeline'))
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->label(__('waiter_requests.fields.created_at'))
                            ->dateTime(),
                        Infolists\Components\TextEntry::make('acknowledged_at')
                            ->label(__('waiter_requests.fields.acknowledged_at'))
                            ->dateTime()
                            ->placeholder(__('waiter_requests.placeholders.not_acknowledged')),
                        Infolists\Components\TextEntry::make('completed_at')
                            ->label(__('waiter_requests.fields.completed_at'))
                            ->dateTime()
                            ->placeholder(__('waiter_requests.placeholders.not_completed')),
                    ])
                    ->columns(3),
                    
                Infolists\Components\Section::make(__('waiter_requests.sections.additional_info'))
                    ->schema([
                        Infolists\Components\TextEntry::make('note')
                            ->label(__('waiter_requests.fields.note'))
                            ->placeholder(__('waiter_requests.placeholders.no_note')),
                    ]),
            ]);
    }
}
