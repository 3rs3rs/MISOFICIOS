<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Queja extends Model
{
    protected $table = 'quejas';
    protected $fillable = ['id', 'mpio_id', 'tipo', 'nombres', 'apellido_paterno', 'apellido_materno', 'domicilio', 'colonia',
            'municipio', 'num_telefono', 'email', 'conoce_nombre', 'nombre_descripcion', 'departamento_id', 'fecha_hechos',
            'descripcion_apliamente', 'pruebas', 'adjuntar_pruebas', 'testigos', 'anonimo', 'fecha_queja', 'activo'];
    public function mpio()
        {
            return $this->belongsTo(Mpio::class, 'mpio_id', 'id');     // cada queja pertenece a un mpio 'mpio_id'
        }
    public function departamento()
        {
            return $this->belongsTo(Departamento::class, 'departamento_id', 'id');     // cada queja pertenece a un departamento 'departamento_id'
        }
    // Borra la Imagen del adjuntar_pruebas, cuando se eOprima el Boton [Borrar] y no se quede guardada en /public/storage/quejas si ya no se va a utilizar
    protected static function booted()
        {
            static::deleting(function ($queja) {
                if ($queja->adjuntar_pruebas && Storage::disk('quejas_public')->exists($queja->adjuntar_pruebas)) {
                    Storage::disk('quejas_public')->delete($queja->adjuntar_pruebas);
                }
            });
        }
}
