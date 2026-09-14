<?php

namespace App\Mail;

use App\Models\Oficio;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class OficioEnviado extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Oficio $oficio) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Oficio '.$this->oficio->numero_unico.' - '.$this->oficio->asunto,
        );
    }

    public function content(): Content
    {
        $pdf = $this->oficio->getFirstMedia('pdfs');

        return new Content(
            markdown: 'emails.oficio-enviado',
            with: [
                'pdf' => $pdf ? $this->datosDocumento($pdf) : null,
                'adjuntos' => $this->oficio
                    ->getMedia('adjuntos')
                    ->map(fn (Media $media) => $this->datosDocumento($media))
                    ->all(),
            ],
        );
    }

    public function attachments(): array
    {
        return $this->oficio
            ->getMedia('pdfs')
            ->merge($this->oficio->getMedia('adjuntos'))
            ->map(fn ($media) => Attachment::fromPath($media->getPath())
                ->as($this->nombreArchivo($media))
                ->withMime($media->mime_type ?? 'application/pdf'))
            ->all();
    }

    private function nombreArchivo(Media $media): string
    {
        $extension = pathinfo($media->file_name, PATHINFO_EXTENSION);

        return $extension === '' ? $media->name : $media->name.'.'.$extension;
    }

    private function datosDocumento(Media $media): array
    {
        return [
            'nombre' => $this->nombreArchivo($media),
            'url' => URL::signedRoute('oficios.documentos.ver', ['media' => $media]),
        ];
    }
}
