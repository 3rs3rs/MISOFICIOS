<?php

namespace App\Filament\Resources\Departamentos\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class DepartamentoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Se guarda en BD, pero no se muestra.
                // Solo se fija al crear para no cambiar el municipio al editar.
                Hidden::make('mpio_id')
                    ->default(fn () => auth()->user()?->mpio_id ?? 1)
                    ->dehydrated(fn (?string $operation) => $operation === 'create'),

                TextInput::make('nombre')
                    ->label('Nombre del Departamento')
                    ->maxLength(50)
                    ->nullable(),

                TextInput::make('director')
                    ->label('Nombre del Director')
                    ->maxLength(50)
                    ->nullable(),

                TextInput::make('clave')
                    ->label('Clave')
                    ->maxLength(10)
                    ->unique(ignoreRecord: true)
                    ->nullable(),

                TextInput::make('email_contacto')
                    ->label('Email de contacto')
                    ->email()
                    ->maxLength(255)
                    ->nullable(),

                Toggle::make('activo')
                    ->label('Activo')
                    ->default(true),
            ]);
    }
}
