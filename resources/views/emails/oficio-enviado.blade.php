<x-oficio-mail.message :municipio="$oficio->remitente?->mpio?->nombre">
# Oficio {{ $oficio->numero_unico }}

Se te ha enviado el siguiente oficio:

**Remitente:** {{ $oficio->remitente?->nombre }}
**Número:** {{ $oficio->numero_unico }}
**Tipo de Oficio:** {{ $oficio->tipo }}
**Prioridad:** {{ $oficio->prioridad }}
@if ($oficio->requiere_respuesta)
**Plazo:** {{ $oficio->plazo_dias }} día(s) · Fecha límite: {{ optional($oficio->fecha_limite)->format('d/m/Y') }}
@endif

## Asunto

{{ $oficio->asunto }}

## Contenido

{!! $oficio->cuerpo_html !!}

@if ($pdf)
El PDF del oficio se adjunta a este correo.

**{{ $pdf['nombre'] }}**

<x-mail::button :url="$pdf['url']">
Ver PDF
</x-mail::button>
@endif

@if (count($adjuntos) > 0)
## Documentos adjuntos

Este correo también incluye {{ count($adjuntos) }} documento(s) adjunto(s):

@foreach ($adjuntos as $adjunto)
**{{ $adjunto['nombre'] }}**

<x-mail::button :url="$adjunto['url']">
Ver PDF
</x-mail::button>
@endforeach
@endif

@if ($oficio->requiere_respuesta)
<x-mail::subcopy>
Este oficio requiere respuesta antes de la fecha límite.
</x-mail::subcopy>
@endif

Saludos cordiales.
</x-oficio-mail.message>
