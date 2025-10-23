<?php

namespace App\Filament\Resources\WaiterRequestResource\Pages;

use App\Filament\Resources\WaiterRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWaiterRequest extends EditRecord
{
    protected static string $resource = WaiterRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
