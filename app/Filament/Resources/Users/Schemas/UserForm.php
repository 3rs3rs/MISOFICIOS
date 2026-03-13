<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class UserForm
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

/*                Select::make('mpio_id')
                    ->label('Municipio')
                    ->relationship('mpio', 'nombre')
                    ->searchable()
                    ->preload()
                    ->required(), */
                Select::make('departamento_id')
                    ->label('Departamento')
                    ->relationship('departamento', 'nombre')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('name')
                    ->label('Nombre de Usuario:')
                    ->required(),
                TextInput::make('email')
                    ->label('Correo Electronico:')
                    ->email()
                    ->required(),
                TextInput::make('password')
                    ->password()
                    ->required(),
                Select::make('status')
                    ->options(['Master' => 'Master', 'Admin' => 'Admin', 'Operador' => 'Operador'])
                    ->default('Admin')
                    ->required(),
                Select::make('activo')
                    ->options(['Si' => 'Si', 'No' => 'No'])
                    ->default('Si')
                    ->required(),
            ]);
    }
}