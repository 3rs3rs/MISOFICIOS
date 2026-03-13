<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Respuesta extends Model
{
    use HasFactory;

    protected $fillable = [
    'oficio_id','autor_id','cuerpo_html','pdf_path','fecha_respuesta'
    ];

    public function oficio() {
    return $this->belongsTo(Oficio::class);
    }

    public function autor() {
    return $this->belongsTo(Usuario::class, 'autor_id');
    }
}