<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationGroup = null;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function getModelLabel(): string
    {
        return __('filament.models.payment');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.models.payments');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.nav.restaurant');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('method')->label(__('filament.fields.payment'))->options([
                'idram' => __('filament.methods.idram'),
                'telcell' => __('filament.methods.telcell'),
                'visa' => __('filament.methods.visa'),
                'mastercard' => __('filament.methods.mastercard'),
                'cash' => __('filament.methods.cash'),
            ])->required(),
            TextInput::make('amount')->label(__('filament.fields.price'))->numeric()->required(),
            TextInput::make('transaction_id')->label(__('filament.fields.transaction_id')),
            Select::make('status')->label(__('filament.fields.status'))->options([
                'success' => __('filament.statuses.success'),
                'failed' => __('filament.statuses.failed'),
                'pending' => __('filament.statuses.pending'),
            ])->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('order.id')->label(__('filament.fields.order')),
                TextColumn::make('amount')->label(__('filament.fields.price'))->money('AMD')->sortable(),
                BadgeColumn::make('status')
                    ->colors([
                        'success' => 'success',
                        'danger' => 'failed',
                        'warning' => 'pending',
                    ]),
                TextColumn::make('transaction_id')->label(__('filament.fields.transaction_id'))->toggleable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('refund')
                    ->label(__('filament.actions.refund'))
                    ->requiresConfirmation()
                    ->modalHeading(__('filament.actions.refund'))
                    ->modalSubmitActionLabel(__('filament.actions.refund'))
                    ->visible(fn (Payment $record) => $record->status === 'success')
                    ->action(function (Payment $record) {
                        $order = $record->order;
                        $order->update(['payment_status' => 'refunded']);
                        $record->update(['status' => 'failed', 'paid_at' => null]);
                    }),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListPayments::route('/'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
