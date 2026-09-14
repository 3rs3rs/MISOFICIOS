<?php

namespace App\Filament\Resources\Departamentos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class DepartamentosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->label('Departamento')
                    ->searchable(),

                TextColumn::make('director')
                    ->searchable(),

                TextColumn::make('tel_celular')
                    ->searchable(),

                TextColumn::make('clave')
                    ->searchable(),

                TextColumn::make('email_contacto')
                    ->searchable(),

/*                TextColumn::make('mpio.nombre')
                    ->label('Municipio')
                    ->toggleable(),       */

                IconColumn::make('activo')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Group::make('mpio_id')
                    ->label('Municipio')
                    ->getTitleFromRecordUsing(
                        fn ($record) => $record->mpio?->nombre ?? 'Sin municipio'
                    )
                    ->collapsible(),
            ])
            ->defaultGroup('mpio_id')
            ->filters([
                //
            ])
            ->recordActions([
//                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
