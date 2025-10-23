<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WaiterRequestResource\Pages;
use App\Models\WaiterRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;

class WaiterRequestResource extends Resource
{
    protected static ?string $model = WaiterRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell-alert';
    
    protected static ?string $navigationGroup = 'Operations';
    
    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('waiter_requests.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('waiter_requests.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('waiter_requests.plural_label');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $pendingCount = static::getModel()::where('status', 'pending')->count();
        
        if ($pendingCount > 5) {
            return 'danger';
        } elseif ($pendingCount > 0) {
            return 'warning';
        }
        
        return 'success';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('restaurant_id')
                    ->relationship('restaurant', 'name')
                    ->required()
                    ->disabled(),
                    
                Forms\Components\Select::make('table_id')
                    ->relationship('table', 'number')
                    ->required()
                    ->disabled(),
                    
                Forms\Components\Select::make('order_id')
                    ->relationship('order', 'id')
                    ->disabled(),
                    
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => __('waiter_requests.statuses.pending'),
                        'acknowledged' => __('waiter_requests.statuses.acknowledged'),
                        'completed' => __('waiter_requests.statuses.completed'),
                    ])
                    ->required()
                    ->default('pending'),
                    
                Forms\Components\Textarea::make('note')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                    
                Forms\Components\DateTimePicker::make('acknowledged_at')
                    ->disabled(),
                    
                Forms\Components\DateTimePicker::make('completed_at')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('waiter_requests.fields.id'))
                    ->sortable(),
                    
                Tables\Columns\BadgeColumn::make('status')
                    ->label(__('waiter_requests.fields.status'))
                    ->colors([
                        'danger' => 'pending',
                        'warning' => 'acknowledged',
                        'success' => 'completed',
                    ])
                    ->icons([
                        'heroicon-o-clock' => 'pending',
                        'heroicon-o-eye' => 'acknowledged',
                        'heroicon-o-check-circle' => 'completed',
                    ])
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('table.number')
                    ->label(__('waiter_requests.fields.table'))
                    ->sortable()
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('order.id')
                    ->label(__('waiter_requests.fields.order_id'))
                    ->sortable()
                    ->url(fn ($record) => $record->order_id 
                        ? route('filament.admin.resources.orders.edit', $record->order_id)
                        : null),
                    
                Tables\Columns\TextColumn::make('note')
                    ->label(__('waiter_requests.fields.note'))
                    ->limit(50)
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('waiter_requests.fields.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->since(),
                    
                Tables\Columns\TextColumn::make('acknowledged_at')
                    ->label(__('waiter_requests.fields.acknowledged_at'))
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->placeholder('—'),
                    
                Tables\Columns\TextColumn::make('response_time')
                    ->label(__('waiter_requests.fields.response_time'))
                    ->getStateUsing(function ($record) {
                        if (!$record->acknowledged_at) {
                            return '—';
                        }
                        $seconds = $record->created_at->diffInSeconds($record->acknowledged_at);
                        return $seconds . 's';
                    })
                    ->badge()
                    ->color(fn ($state) => match(true) {
                        $state === '—' => 'gray',
                        (int)$state <= 30 => 'success',
                        (int)$state <= 60 => 'warning',
                        default => 'danger',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'acknowledged' => 'Acknowledged',
                        'completed' => 'Completed',
                    ]),
                    
                Tables\Filters\Filter::make('pending_only')
                    ->label('Pending Only')
                    ->query(fn (Builder $query) => $query->where('status', 'pending'))
                    ->toggle(),
                    
                Tables\Filters\Filter::make('today')
                    ->label('Today')
                    ->query(fn (Builder $query) => $query->whereDate('created_at', today()))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\Action::make('acknowledge')
                    ->label(__('waiter_requests.actions.acknowledge'))
                    ->icon('heroicon-o-eye')
                    ->color('warning')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->acknowledge();
                        
                        Notification::make()
                            ->title(__('waiter_requests.notifications.acknowledged'))
                            ->success()
                            ->body(__('waiter_requests.notifications.acknowledged_body', ['table' => $record->table->number]))
                            ->send();
                    }),
                    
                Tables\Actions\Action::make('complete')
                    ->label(__('waiter_requests.actions.complete'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->status === 'acknowledged')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->complete();
                        
                        Notification::make()
                            ->title(__('waiter_requests.notifications.completed'))
                            ->success()
                            ->body(__('waiter_requests.notifications.completed_body', ['table' => $record->table->number]))
                            ->send();
                    }),
                    
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('acknowledge_all')
                        ->label('Acknowledge Selected')
                        ->icon('heroicon-o-eye')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $count = 0;
                            foreach ($records as $record) {
                                if ($record->status === 'pending') {
                                    $record->acknowledge();
                                    $count++;
                                }
                            }
                            
                            Notification::make()
                                ->title('Requests Acknowledged')
                                ->success()
                                ->body("{$count} requests acknowledged.")
                                ->send();
                        }),
                        
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->poll('5s'); // Auto-refresh every 5 seconds
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWaiterRequests::route('/'),
            'create' => Pages\CreateWaiterRequest::route('/create'),
            'view' => Pages\ViewWaiterRequest::route('/{record}'),
            'edit' => Pages\EditWaiterRequest::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with(['restaurant', 'table', 'order']);

        // Super-admin sees all
        $user = auth()->user();
        if ($user && $user->hasRole('super-admin')) {
            return $query;
        }

        // Other users see only their restaurant's requests
        if ($user && $user->restaurant_id) {
            return $query->where('restaurant_id', $user->restaurant_id);
        }

        return $query;
    }
}
