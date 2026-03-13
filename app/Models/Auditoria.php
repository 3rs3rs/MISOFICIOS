<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Auditoria extends Model
{
    use HasFactory;

    protected $table = 'auditoria';

    protected $fillable = ['user_id','accion','modelo','modelo_id','cambios_json','ip'];

    public function user() {
    return $this->belongsTo(Usuario::class);
    }
}