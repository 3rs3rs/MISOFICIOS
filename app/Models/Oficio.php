<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Oficio extends Model implements HasMedia
{
    use HasFactory;
    use SoftDeletes;
    use InteractsWithMedia;

    protected $table = 'oficios';

    protected $fillable = [
        'numero_unico',
        'remitente_id',
        'destinatario_id',
        'creador_id',
        'asunto',
        'cuerpo_html',
        'requiere_respuesta',
        'plazo_dias',
        'fecha_limite',
        'estado',
        'prioridad',
        'tiene_adjuntos',
        'fecha_envio',
        'fecha_recibido',
        'recibido_por',
        'fecha_respuesta',
        'tipo',
        'hash_documento',
        'fecha_visto',
        'cerrado_por',
        'fecha_cierre',
        'motivo_cierre',
    ];

    protected $casts = [
        'requiere_respuesta' => 'boolean',
        'tiene_adjuntos' => 'boolean',
        'plazo_dias' => 'integer',

        'fecha_limite' => 'date',
        'fecha_envio' => 'datetime',
        'fecha_recibido' => 'datetime',
        'fecha_respuesta' => 'datetime',
        'fecha_visto' => 'datetime',
        'fecha_cierre' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function remitente(): BelongsTo
    {
        return $this->belongsTo(
            Departamento::class,
            'remitente_id'
        );
    }

    public function destinatario(): BelongsTo
    {
        return $this->belongsTo(
            Departamento::class,
            'destinatario_id'
        );
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'creador_id'
        );
    }

    public function recibidoPor(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'recibido_por'
        );
    }

    public function cerradoPor(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'cerrado_por'
        );
    }

    public function respuestas(): HasMany
    {
        return $this->hasMany(Respuesta::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Numeración del oficio
    |--------------------------------------------------------------------------
    */

    public static function generarNumeroParaDepartamento(
        int $departamentoId
    ): string {
        $departamento = Departamento::find($departamentoId);

        if (! $departamento || blank($departamento->clave)) {
            return '';
        }

        $year = now()->year;

        $ultimoNumero = self::query()
            ->where('remitente_id', $departamentoId)
            ->whereYear('created_at', $year)
            ->whereNotNull('numero_unico')
            ->orderByDesc('id')
            ->value('numero_unico');

        $consecutivo = 1;

        /*
         * Formato esperado:
         * TES/007/2026
         */
        if (
            $ultimoNumero
            && preg_match(
                '~^[^/]+/(\d+)/(\d{4})$~',
                $ultimoNumero,
                $coincidencias
            )
        ) {
            $ultimoConsecutivo = (int) $coincidencias[1];
            $ultimoYear = (int) $coincidencias[2];

            if ($ultimoYear === $year) {
                $consecutivo = $ultimoConsecutivo + 1;
            }
        }

        return sprintf(
            '%s/%03d/%d',
            mb_strtoupper($departamento->clave),
            $consecutivo,
            $year
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Spatie Media Library
    |--------------------------------------------------------------------------
    */

    public function registerMediaCollections(): void
    {
        /*
         * PDF principal del oficio.
         * Solo debe existir uno por oficio.
         */
        $this->addMediaCollection('pdfs')
            ->acceptsMimeTypes(['application/pdf'])
            ->singleFile()
            ->useDisk('local');

        /*
         * PDFs adicionales enviados junto con el oficio.
         * Se permiten varios archivos.
         */
        $this->addMediaCollection('adjuntos')
            ->acceptsMimeTypes(['application/pdf'])
            ->useDisk('local');
    }

    /*
    |--------------------------------------------------------------------------
    | Eventos del modelo
    |--------------------------------------------------------------------------
    */

    protected static function booted(): void
    {
        static::creating(function (Oficio $oficio): void {
            $usuario = auth()->user();

            if (! $usuario) {
                return;
            }

            $oficio->creador_id = $usuario->id;
            $oficio->remitente_id = $usuario->departamento_id;
        });
    }
}
