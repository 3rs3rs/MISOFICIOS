<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
        protected $table = 'departamentos';
        protected $fillable = ['id','mpio_id','nombre','director','clave','email_contacto','activo'];

        public function mpio() {
            return $this->belongsTo(Mpio::class, 'mpio_id', 'id');     // cada departamento pertenece a un mpio 'mpio_id'
            }

        public function usuarios() {
            return $this->hasMany(Usuario::class);
            }

        public function oficiosRemitidos() {
            return $this->hasMany(Oficio::class, 'remitente_id');
            }

        public function oficiosRecibidos() {
            return $this->hasMany(Oficio::class, 'destinatario_id');
            }

        protected static function booted() {
            static::creating(function ($departamento) {
                if (auth()->check()) {
                    $departamento->mpio_id = auth()->user()->mpio_id;
                }
            });
        }
}