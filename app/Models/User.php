<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'mpio_id',
        'departamento_id',
        'name',
        'email',
        'status',
        'password',
        'activo'
    ]; 

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */

    protected $hidden = [ 'password','remember_token' ];

    //** (belongsTo = Pertenece a) Un departamento quien es su municipio  */

    public function mpio()
    {
        return $this->belongsTo(Mpio::class, 'mpio_id');
    }

    public function departamento() {
        return $this->belongsTo(Departamento::class, 'departamento_id');
    }

    public function oficiosCreados() {
        return $this->hasMany(Oficio::class, 'creador_id');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

}
