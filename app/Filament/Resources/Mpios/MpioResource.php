<?php

namespace App\Filament\Resources\Mpios;

use App\Filament\Resources\Mpios\Pages\ManageMpios;
use App\Models\Mpio;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;




class MpioResource extends Resource
{
    protected static ?string $model = Mpio::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

//    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static ?string $navigationLabel = 'Municipios';
    protected static ?string $pluralModelLabel = 'Municipios';
    protected static ?string $modelLabel = 'Municipio';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(255),
                    //--------------------------------------------------------------/
                Forms\Components\FileUpload::make('logotipo')
                    ->label('Logotipo de Contraloria o Municipio')
                    ->image()                     // Valida que sea imagen
                    ->acceptedFileTypes(['image/jpeg', 'image/png']) // tipos permitidos
                    ->maxSize(2048)               // máximo 2MB (en KB)
                    ->preserveFilenames()         // (opcional) mantiene el nombre original
                    ->disk('oficios_public')       // Disco donde está almacenada la imagen                    
                    //->visibility('public')      // Guarda en disco público
                    ->imagePreviewHeight('150')   // Altura de la vista previa
                    ->required()                  // o ->nullable()
                    ->deleteUploadedFileUsing(static function ($file) {   // Elimina el Logo al momento de oprimir (X) y Cambiar el Logo
                        return Storage::disk('oficios_public')->delete($file);
                    }),
                //--------------------------------------------------------------/
                Select::make('activo')
                    ->label('Activo?')
                    ->required()
                    ->placeholder('Seleccione una opción')
                    ->options([
                        'Si' => 'Sí',
                        'No' => 'No',
                ]),                        
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable(),
/*
                    ImageColumn::make('logotipo')
                    ->label('Logotipo')
                    ->disk('quejas_public')     // Disco donde está almacenada la imagen
                    ->url(fn ($state) => Storage::disk('quejas_public')->url($state))
                    ->openUrlInNewTab()
                    ->simpleLightbox()
                    ->height(60),        // Altura deseada
*/
                Tables\Columns\ImageColumn::make('logotipo')
                    ->label('Logo')
                    ->disk('oficios_public')
                    ->height(40)
                    ->circular()
                    ->url(fn ($record) => $record->logotipo 
                        ? \Illuminate\Support\Facades\Storage::disk('oficios_public')->url($record->logotipo)
                        : null,
                        true // abrir en nueva pestaña
                    ),

                Tables\Columns\TextColumn::make('activo'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageMpios::route('/'),
        ];
    }
}
