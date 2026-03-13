<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;


class Oficio extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia; // ← ESTA LÍNEA ES CLAVE
    
    protected $table = 'oficios';

    protected $fillable = [
        'id',
        'remitente_id',
        'destinatario_id',
        'numero_unico',
        'prioridad',
        'estado',
        'asunto',
        'cuerpo_html',
        'creador_id',
        'requiere_respuesta',
        'plazo_dias',
        'fecha_limite',
        'pdf_path',
        'tiene_adjuntos',
    ];

    public function remitente() {
    return $this->belongsTo(Departamento::class, 'remitente_id');
    }

    public function destinatario() {
    return $this->belongsTo(Departamento::class, 'destinatario_id');
    }

    public function creador() {
    return $this->belongsTo(User::class, 'creador_id');
    }

    public function respuestas() {
    return $this->hasMany(Respuesta::class);
    }

    public function adjuntos() {
    return $this->hasMany(Adjunto::class);
    }
/*
    public static function generarNumeroParaDepartamento($departamentoId): string
    {
        $departamento = Departamento::find($departamentoId);

        if (!$departamento) {
            return '';
        }

        $ultimo = self::where('remitente_id', $departamentoId)
            ->orderByDesc('id')
            ->first();

        $consecutivo = $ultimo
            ? intval(substr($ultimo->numero_unico, -6)) + 1
            : 1;

        return strtoupper($departamento->clave)
            . '/' . str_pad($consecutivo, 3, '0', STR_PAD_LEFT) . '/'
            . now()->year;
    }
*/
    public static function generarNumeroParaDepartamento(int $departamentoId): string
    {
        $departamento = Departamento::find($departamentoId);

        if (! $departamento || empty($departamento->clave)) {
            return '';
        }

        $year = now()->year;

        $ultimo = self::query()
            ->where('remitente_id', $departamentoId)
            ->whereYear('created_at', $year)
            ->orderByDesc('id')
            ->value('numero_unico'); // más ligero que ->first()

        $consecutivo = 1;

        // Espera formato: CLAVE/NNN/YYYY (ej. TES/007/2026)
        if ($ultimo && preg_match('~^[^/]+/(\d+)/(\d{4})$~', $ultimo, $m)) {
            $ultimoNum = (int) $m[1];
            $ultimoYear = (int) $m[2];

            if ($ultimoYear === $year) {
                $consecutivo = $ultimoNum + 1;
            }
        }

        return strtoupper($departamento->clave)
            . '/' . str_pad((string) $consecutivo, 3, '0', STR_PAD_LEFT)
            . '/' . $year;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('pdfs')
            ->acceptsMimeTypes(['application/pdf'])
            //->singleFile()
            //->useDisk('pdfs_public');
            ->useDisk('pdfs_public');
    }

    protected static function booted()
    {
        static::creating(function (Oficio $oficio) {
            if (auth()->check()) {
                $oficio->creador_id = auth()->id();
                $oficio->remitente_id = auth()->user()->departamento_id;
            }
        });
    }

}