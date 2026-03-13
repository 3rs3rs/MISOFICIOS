<?php

namespace App\Filament\Resources\Departamentos\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class DepartamentoInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('mpio_id')
                    ->numeric(),
                TextEntry::make('nombre')
                    ->placeholder('-'),
                TextEntry::make('director')
                    ->placeholder('-'),
                TextEntry::make('clave')
                    ->placeholder('-'),
                TextEntry::make('email_contacto')
                    ->placeholder('-'),
                IconEntry::make('activo')
                    ->boolean(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
