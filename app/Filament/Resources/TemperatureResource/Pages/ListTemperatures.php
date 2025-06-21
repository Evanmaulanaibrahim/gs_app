<?php

namespace App\Filament\Resources\TemperatureResource\Pages;

use App\Filament\Resources\TemperatureResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTemperatures extends ListRecords
{
    protected static string $resource = TemperatureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
