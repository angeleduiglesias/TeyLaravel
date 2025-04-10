<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
//use Laravel\Sanctum\HasApiTokens;       //para usar Sanctum

class User extends Authenticatable
{
    use HasFactory, Notifiable; //HasApiTokens;  

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'email',                   //borre el full name, porque no se utiliza
        'password',
        'rol',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
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

    /**
     * Relación uno a uno con Cliente.
     *
     * Cada usuario tendrá un único registro en la tabla clientes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function cliente(): HasOne
    {
        // Indicamos que un usuario tiene un cliente.
        return $this->hasOne(Cliente::class, 'user_id');
    }

    public function admin(): HasOne
    {
        // Indicamos que un usuario tiene un admin.
        return $this->hasOne(Admin::class, 'user_id');
    }
}
