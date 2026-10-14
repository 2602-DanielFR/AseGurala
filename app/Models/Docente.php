<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Docente extends Model
{
    protected $table = 'docentes';
    protected $fillable = [
        'dni',
        'nombres',
        'apellidos',
        'email',
        'estado',
    ];
}
