<?php

namespace App\Filament\Resources\Oficios\Schemas;

use App\Models\Oficio;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Carbon;
use App\Services\BusinessDaysService;
use Filament\Forms\Components\Toggle;

// Si usas el plugin/componente de Spatie en Filament:
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class OficioForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Datos del Oficio')
                ->description('Captura la información principal del oficio.')
                ->icon('heroicon-o-document-text')
                ->columns(12)
                ->schema([

                    Select::make('creador_id')
                        ->label('Creador del Oficio')
                        ->relationship('creador', 'name') // 👈 relación correcta
                        ->default(fn () => auth()->id())  // 👈 ID del usuario
                        ->disabled()
                        ->dehydrated()
                        ->required()
                        ->columnSpan(4),

                    // Remitente = departamento del usuario logueado
                    Select::make('remitente_id')
                        ->label('Departamento remitente')
                        ->relationship('remitente', 'nombre')
                        ->default(fn () => auth()->user()?->departamento_id)
                        ->disabled()
                        ->dehydrated()
                        ->required()
                        ->columnSpan(4),

                    // Destinatario (mismo mpio y activo)
                    Select::make('destinatario_id')
                        ->label('Departamento destinatario')
                        ->relationship(
                            name: 'destinatario',
                            titleAttribute: 'nombre',
                            modifyQueryUsing: function ($query) {
                                $mpioId = auth()->user()?->mpio_id;

                                return $query
                                    ->when($mpioId, fn ($q) => $q->where('mpio_id', $mpioId))
                                    ->where('activo', true);
                            }
                        )
                        ->searchable()
                        ->preload()
                        ->required()
                        ->columnSpan(4),

                    // Número único autogenerado
                    TextInput::make('numero_unico')
                        ->label('Folio único')
                        ->unique(ignoreRecord: true)
                        ->disabled()
                        ->dehydrated()
                        ->default(function () {
                            $remitenteId = auth()->user()?->departamento_id;

                            return $remitenteId
                                ? Oficio::generarNumeroParaDepartamento($remitenteId)
                                : null;
                        })
                        ->columnSpan(4),

                    Select::make('prioridad')
                        ->label('Prioridad')
                        ->options([
                            'BAJA' => 'BAJA',
                            'NORMAL' => 'NORMAL',
                            'ALTA' => 'ALTA',
                            'URGENTE' => 'URGENTE',
                        ])
                        ->default('NORMAL')
                        ->required()
                        ->native(false)
                        ->columnSpan(4),

                    Select::make('estado')
                        ->label('Estado')
                        ->options([
                            'BORRADOR' => 'BORRADOR',
                        //  'ENVIADO' => 'ENVIADO',
                        //  'RECIBIDO' => 'RECIBIDO',
                        //  'EN_RESPUESTA' => 'EN_RESPUESTA',
                        //  'RESPONDIDO' => 'RESPONDIDO',
                        //  'VENCIDO' => 'VENCIDO',
                        //  'CERRADO' => 'CERRADO',
                        ])
                        ->default('BORRADOR')
                        ->required()
                        ->native(false)
                        ->columnSpan(4),

                    TextInput::make('asunto')
                        ->label('Asunto')
                        ->maxLength(255)
                        ->nullable()
                        ->columnSpan(12),

                    RichEditor::make('cuerpo_html')
                        ->label('Cuerpo del oficio')
                        ->nullable()
                        ->columnSpan(12),

                    Toggle::make('requiere_respuesta')
                        ->label('¿Requiere contestación?')
                        ->default(false)
                        ->live()
                        ->afterStateUpdated(function (Set $set, ?bool $state) {
                            if (! $state) {
                                // Si NO requiere respuesta
                                $set('plazo_dias', null);
                                $set('fecha_limite', null);
                            } else {
                                // Si requiere respuesta
                                $set('plazo_dias', 3);
                                $set('fecha_limite', Carbon::today()->addDays(3)->toDateString());
                            }
                        })
                        ->helperText('Si activas esta opción, podrás Seleccionar los dias de plazo para la Contentacion.')
                        ->columnSpan(12),

                    TextInput::make('plazo_dias')
                        ->label('Plazo (días)')
                        ->numeric()
                        ->minValue(1)
                        ->default(3)
                        ->live()
                        ->visible(fn (Get $get): bool => (bool) $get('requiere_respuesta'))
                        ->required(fn (Get $get): bool => (bool) $get('requiere_respuesta'))
                        ->afterStateUpdated(function (Get $get, Set $set, $state) {
                            if (! (bool) $get('requiere_respuesta')) {
                                return;
                            }

                            $dias = max((int) ($state ?? 1), 1);

                            // 👇 Cálculo con Service (días hábiles)
                            $fechaLimite = BusinessDaysService::add(Carbon::today(), $dias);

                            $set('fecha_limite', $fechaLimite->toDateString()); // Y-m-d
                        })
                        ->columnSpan(6),

                    DatePicker::make('fecha_limite')
                        ->label('Fecha límite')
                        ->native(false)
                        ->displayFormat('d/m/Y') // lo que ve el usuario
                        ->format('Y-m-d')        // formato BD
                        ->visible(fn (Get $get): bool => (bool) $get('requiere_respuesta'))
                        ->required(fn (Get $get): bool => (bool) $get('requiere_respuesta'))
                        ->columnSpan(6),

                    SpatieMediaLibraryFileUpload::make('pdfs')
                        ->label('PDF del oficio')
                        ->collection('pdfs')          // coincide con registerMediaCollections()
                        ->acceptedFileTypes(['application/pdf'])
                        ->maxSize(20480)              // 20 MB
                        ->helperText('Puedes cargar solamente un PDF por oficio (máx 20 MB).')
                        ->multiple(false)             // un solo PDF por oficio
                        ->downloadable()
                        ->openable()
                        ->preserveFilenames()
                        ->columnSpan(12),
                ]), 

            Section::make('Adjuntos')
                ->description('Adjunta PDFs relacionados con el oficio.')
                ->icon('heroicon-o-paper-clip')
                ->columns(12)
                ->schema([
                    Toggle::make('tiene_adjuntos')
                        ->label('¿Aceptará documentos adjuntos?')
                        ->default(false)
                        ->live()
                        ->afterStateUpdated(function (Set $set, ?bool $state) {
                            if (! $state) {
                                // Si NO llevará adjuntos, solo apagamos la UI.
                                // (Recomendación) No borrar archivos automáticamente aquí.
                                // Si quieres, podemos agregar una acción explícita "Eliminar adjuntos".
                            }
                        })
                        ->helperText('Si activas esta opción, podrás cargar uno o varios PDFs.')
                        ->columnSpan(12),
                /*
                    SpatieMediaLibraryFileUpload::make('pdfs')
                        ->label('PDF(s)')
                        ->collection('pdfs')
                        ->multiple()
                        ->acceptedFileTypes(['application/pdf'])
                        ->maxFiles(10)
                        ->columnSpan(12),
                */
                    SpatieMediaLibraryFileUpload::make('adjuntos')
                        ->label('Adjuntar PDFs')
                        ->collection('adjuntos')
                        ->multiple()
                        ->reorderable()
                        ->downloadable()
                        ->openable()
                        ->acceptedFileTypes(['application/pdf'])
                        ->maxSize(10240) // 10MB por archivo
                        ->helperText('Puedes cargar tantos PDFs como sean necesarios (máx 10MB c/u).')
                        ->visible(fn (\Filament\Schemas\Components\Utilities\Get $get): bool => (bool) $get('tiene_adjuntos'))
                        ->required(fn (\Filament\Schemas\Components\Utilities\Get $get): bool => (bool) $get('tiene_adjuntos'))
                        ->columnSpan(12),                        
                ]),

        ]);
    }
}
