<?php

namespace App\Http\Controllers;

use App\Models\ResultadoChaside;
use App\Support\Chaside;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ShareController extends Controller
{
    public function ver(string $token): View
    {
        $resultado = ResultadoChaside::with('estudiante.colegio')
            ->where('share_token', $token)
            ->firstOrFail();

        $puntajes = $resultado->puntajes();

        return view('pages.resultado_share', [
            'resultado'  => $resultado,
            'estudiante' => $resultado->estudiante,
            'puntajes'   => $puntajes,
            'maximo'     => max(max($puntajes), 1),
            'areas'      => Chaside::AREAS,
            'ordenAreas' => Chaside::ORDEN_AREAS,
            'principal'  => $resultado->area_principal,
            'secundaria' => $resultado->area_secundaria,
            'shareToken' => $token,
        ]);
    }

    public function pdf(string $token)
    {
        $resultado = ResultadoChaside::with('estudiante.colegio')
            ->where('share_token', $token)
            ->firstOrFail();

        $puntajes = $resultado->puntajes();

        $pdf = Pdf::loadView('pdf.v4_pro', [
            'resultado'  => $resultado,
            'estudiante' => $resultado->estudiante,
            'puntajes'   => $puntajes,
            'maximo'     => max(max($puntajes), 1),
            'areas'      => Chaside::AREAS,
            'ordenAreas' => Chaside::ORDEN_AREAS,
            'principal'  => $resultado->area_principal,
            'secundaria' => $resultado->area_secundaria,
        ])->setPaper('a4', 'portrait');

        $slug = Str::slug($resultado->estudiante->nombre_completo);

        return $pdf->download("informe-chaside-{$slug}.pdf");
    }
}
