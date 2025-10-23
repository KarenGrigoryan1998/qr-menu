<?php

namespace App\Filament\Resources\TableResource\Pages;

use App\Filament\Resources\TableResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateTable extends CreateRecord
{
    protected static string $resource = TableResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();
        if ($user && $user->restaurant_id && empty($data['restaurant_id'])) {
            $data['restaurant_id'] = $user->restaurant_id;
        }
        return $data;
    }
}
