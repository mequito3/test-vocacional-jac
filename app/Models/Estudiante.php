<?php

namespace App\Models;

use App\Models\Colegio;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Estudiante extends Model
{
    use HasFactory;

    protected $table = 'estudiantes';

    protected $fillable = [
        'colegio_id',
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

    public function colegio(): BelongsTo
    {
        return $this->belongsTo(Colegio::class);
    }

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
