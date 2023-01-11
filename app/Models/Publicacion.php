<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;

class Publicacion extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'publicaciones';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'titulo',
        'nombre',
        'descripcion',
        'promocion',
        'precio',
        'descuento',
        'disponibilidad'
    ];

   
}
