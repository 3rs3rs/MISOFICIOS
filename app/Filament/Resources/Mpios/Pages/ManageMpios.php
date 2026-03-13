<?php

namespace App\Filament\Resources\Mpios\Pages;

use App\Filament\Resources\Mpios\MpioResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageMpios extends ManageRecords
{
    protected static string $resource = MpioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
