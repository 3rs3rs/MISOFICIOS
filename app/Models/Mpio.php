<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Mpio extends Model
{
    protected $table = 'mpios';
    protected $fillable = ['id', 'nombre', 'logotipo', 'activo'];
// Borra la Imagen del adjuntar_pruebas, cuando se Oprima el Boton [Borrar] y no se quede guardada en /public/storage/quejas si ya no se va a utilizar
    protected static function booted()
        {
            static::deleting(function ($file) {
                if ($file->logotipo && Storage::disk('oficios_public')->exists($file->logotipo)) {
                    Storage::disk('oficios_public')->delete($file->logotipo);
                }
            });
        }          
}
