<?php

namespace App\Filament\Resources\RFIDTagResource\Pages;

use App\Filament\Resources\RFIDTagResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRFIDTag extends EditRecord
{
    protected static string $resource = RFIDTagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
