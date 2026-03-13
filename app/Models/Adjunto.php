<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Adjunto extends Model
{
    use HasFactory;

    protected $fillable = [
        'oficio_id','nombre_original','archivo_path','mime_type','size','subido_por'
    ];

    public function oficio() {
        return $this->belongsTo(Oficio::class);
    }

    public function usuario() {
        return $this->belongsTo(User::class, 'subido_por');
    }
}
