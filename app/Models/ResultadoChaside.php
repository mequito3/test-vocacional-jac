<?php

namespace App\Models;

use App\Support\Chaside;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResultadoChaside extends Model
{
    use HasFactory;

    protected $table = 'resultados_chaside';

    protected $fillable = [
        'estudiante_id',
        'puntaje_c',
        'puntaje_h',
        'puntaje_a',
        'puntaje_s',
        'puntaje_i',
        'puntaje_d',
        'puntaje_e',
        'area_principal',
        'area_secundaria',
        'respuestas_json',
        'share_token',
    ];

    protected $casts = [
        'respuestas_json' => 'array',
    ];

    /**
     * Estudiante dueno de este resultado.
     */
    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(Estudiante::class);
    }

    /**
     * Puntajes de las 7 areas como mapa [letra => puntaje].
     *
     * @return array<string,int>
     */
    public function puntajes(): array
    {
        return [
            'C' => $this->puntaje_c,
            'H' => $this->puntaje_h,
            'A' => $this->puntaje_a,
            'S' => $this->puntaje_s,
            'I' => $this->puntaje_i,
            'D' => $this->puntaje_d,
            'E' => $this->puntaje_e,
        ];
    }

    /**
     * Datos descriptivos del area principal.
     */
    public function areaPrincipalData(): array
    {
        return Chaside::AREAS[$this->area_principal];
    }

    /**
     * Datos descriptivos del area secundaria.
     */
    public function areaSecundariaData(): array
    {
        return Chaside::AREAS[$this->area_secundaria];
    }
}
