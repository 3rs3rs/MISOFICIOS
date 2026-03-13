<?php

namespace App\Filament\Resources\Oficios\Schemas;

use App\Models\Oficio;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class OficioInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('numero_unico')
                    ->placeholder('-'),
                TextEntry::make('remitente_id')
                    ->numeric(),
                TextEntry::make('destinatario_id')
                    ->numeric(),
                TextEntry::make('creador_id')
                    ->numeric(),
                TextEntry::make('asunto')
                    ->placeholder('-'),
                TextEntry::make('cuerpo_html')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('plazo_dias')
                    ->numeric(),
                TextEntry::make('fecha_limite')
                    ->date(),
                TextEntry::make('estado')
                    ->badge(),
                TextEntry::make('prioridad')
                    ->badge(),
                TextEntry::make('pdf_path')
                    ->placeholder('-'),
                TextEntry::make('deleted_at')
                    ->dateTime()
                    ->visible(fn (Oficio $record): bool => $record->trashed()),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
