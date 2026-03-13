<?php

namespace App\Filament\Resources\Oficios\Pages;

use App\Filament\Resources\Oficios\OficioResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewOficio extends ViewRecord
{
    protected static string $resource = OficioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
