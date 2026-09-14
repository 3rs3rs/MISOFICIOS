<?php

namespace App\Filament\Resources\Oficios\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Support\Icons\Heroicon;
use App\Mail\OficioEnviado;
use App\Models\Oficio;
use App\Services\WhatsAppService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;

class OficiosTable
{
    public static function configure(Table $table): Table
    {
        return $table
        ->columns([
            TextColumn::make('numero_unico')
            ->label('Número')
            ->searchable()
            ->sortable(),
            
            TextColumn::make('remitente.nombre')
            ->label('Remitente')
            ->searchable()
            ->sortable(),
            
            TextColumn::make('destinatario.nombre')
            ->label('Destinatario')
            ->searchable()
            ->sortable(),
            
            TextColumn::make('estado')
                ->badge()
                ->sortable()
                ->action(
                    Action::make('enviar_a_destinatario')
                        ->label('Enviar a destinatario')
                        ->icon(Heroicon::OutlinedPaperAirplane)
                        ->requiresConfirmation()
                        ->modalHeading('Enviar oficio')
                        ->modalDescription(fn (Oficio $record) => 'Se enviará el oficio '
                            . $record->numero_unico
                            . ' al correo de '
                            . ($record->destinatario?->nombre ?? 'destinatario')
                            . ($record->destinatario?->email_contacto ? ' (' . $record->destinatario->email_contacto . ')' : '')
                            . '.')
                        ->visible(fn (Oficio $record) => $record->hasMedia('pdfs') && $record->destinatario?->email_contacto)
                        ->action(function (Oficio $record) {
                            Mail::to($record->destinatario->email_contacto)
                                ->send(new OficioEnviado($record));

                            $record->update(['estado' => 'enviado']);

                            $phone = $record->destinatario?->tel_celular;
                            $waLink = $phone
                                ? app(WhatsAppService::class)->waLink($phone, 'Le informamos que ha recibido un Oficio de la Contraloría Municipal.'
                                    . ' Número: ' . $record->numero_unico
                                    . '. Asunto: ' . $record->asunto
                                    . '. Revise su correo para consultar el PDF adjunto.')
                                : '';

                            Notification::make()
                                ->title('Oficio enviado')
                                ->body('El oficio ' . $record->numero_unico . ' fue enviado a ' . $record->destinatario->nombre . '.')
                                ->success()
                                ->actions([
                                    Action::make('aviso_whatsapp')
                                        ->label('Avisar por WhatsApp')
                                        ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                                        ->url($waLink)
                                        ->openUrlInNewTab()
                                        ->visible($waLink !== ''),
                                ])
                                ->send();
                        }),
                ),
            
            TextColumn::make('prioridad')
                ->badge()
                ->sortable(),

            TextColumn::make('creador.name')
            ->label('Creado por')
            ->searchable(),
            //->toggleable(isToggledHiddenByDefault: true),
            
            TextColumn::make('pdf_url')
            ->label('URL PDF')
            ->state(fn ($record) => $record->getFirstMediaUrl('pdfs'))
            ->copyable(),
            //->toggleable(isToggledHiddenByDefault: true),                    

                IconColumn::make('pdf')
                    ->label('PDF')
                    ->boolean(fn ($record) => $record->hasMedia('pdfs'))
                    ->trueIcon(Heroicon::OutlinedDocumentText)
                    ->falseIcon(Heroicon::OutlinedXCircle),

                TextColumn::make('asunto')
                    ->label('Asunto')
                    ->searchable()
                    ->limit(50),

                TextColumn::make('plazo_dias')
                    ->label('Plazo')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('fecha_limite')
                    ->label('Fecha límite')
                    ->date()
                    ->sortable(),

                // Si ya usas Media Library, este campo es "legacy".
                // Lo dejo oculto por defecto por si aún lo ocupas.
                TextColumn::make('pdf_path')
                    ->label('PDF (legacy)')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('ver_pdf')
                    ->label('Ver PDF')
                    ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                    ->url(function (Oficio $record): ?string {
                        $media = $record->getFirstMedia('pdfs');

                        if (! $media) {
                            return null;
                        }

                        try {
                            return $media->getTemporaryUrl(now()->addMinutes(30)->endOfHour());
                        } catch (\Throwable) {
                            return $media->getUrl();
                        }
                    })
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => $record->hasMedia('pdfs'))
                    ->tooltip('Abrir el PDF del oficio'),

                // Si quieres volver a habilitar el view:
                // ViewAction::make(),

                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
