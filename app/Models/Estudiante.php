<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Estudiante extends Model
{
    use HasFactory;

    protected $table = 'estudiantes';

    protected $fillable = [
        'nombre',
        'apellido',
        'sexo',
        'edad',
        'celular',
        'email',
        'nombre_madre',
        'celular_madre',
        'nombre_padre',
        'celular_padre',
    ];

    /**
     * Resultados CHASIDE rendidos por el estudiante.
     */
    public function resultados(): HasMany
    {
        return $this->hasMany(ResultadoChaside::class);
    }

    /**
     * Nombre completo del estudiante.
     */
    public function getNombreCompletoAttribute(): string
    {
        return trim("{$this->nombre} {$this->apellido}");
    }
}
