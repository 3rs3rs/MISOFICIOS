<?php

namespace App\Filament\Resources\Oficios\Pages;

use App\Filament\Resources\Oficios\OficioResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOficios extends ListRecords
{
    protected static string $resource = OficioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
