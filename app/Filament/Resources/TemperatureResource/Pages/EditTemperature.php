<?php

namespace App\Filament\Resources\TemperatureResource\Pages;

use App\Filament\Resources\TemperatureResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTemperature extends EditRecord
{
    protected static string $resource = TemperatureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
