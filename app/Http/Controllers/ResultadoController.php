<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use App\Models\ResultadoChaside;
use App\Support\Chaside;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ResultadoController extends Controller
{
    /**
     * Recibe las 98 respuestas, aplica la tabla CHASIDE, guarda el
     * resultado y redirige al informe.
     */
    public function calcular(Request $request): RedirectResponse|JsonResponse
    {
        // Guard: debe existir un estudiante en sesion.
        $estudianteId = $request->session()->get('estudiante_id');
        if (! $estudianteId) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'La sesion del estudiante expiro.'], 401);
            }

            return redirect()->route('registro');
        }

        $request->validate([
            'respuestas' => ['required', 'array', 'size:' . count(Chaside::PREGUNTAS)],
        ]);

        // Normaliza las 98 respuestas a booleanos [numero => bool].
        $entrada = $request->input('respuestas', []);
        $respuestas = [];
        foreach (array_keys(Chaside::PREGUNTAS) as $numero) {
            if (! array_key_exists($numero, $entrada)) {
                abort(422, 'Faltan respuestas del cuestionario.');
            }

            $valor = $entrada[$numero];
            $respuestas[$numero] = filter_var($valor, FILTER_VALIDATE_BOOLEAN);
        }

        $calculo = Chaside::calcular($respuestas);

        $resultado = ResultadoChaside::create([
            'estudiante_id'   => $estudianteId,
            'puntaje_c'       => $calculo['puntajes']['C'],
            'puntaje_h'       => $calculo['puntajes']['H'],
            'puntaje_a'       => $calculo['puntajes']['A'],
            'puntaje_s'       => $calculo['puntajes']['S'],
            'puntaje_i'       => $calculo['puntajes']['I'],
            'puntaje_d'       => $calculo['puntajes']['D'],
            'puntaje_e'       => $calculo['puntajes']['E'],
            'area_principal'  => $calculo['principal'],
            'area_secundaria' => $calculo['secundaria'],
            'respuestas_json' => $respuestas,
            'share_token'     => Str::random(32),
        ]);

        $request->session()->forget('estudiante_id');

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'share_url' => route('resultado.share', $resultado->share_token),
            ]);
        }

        return redirect()->route('welcome');
    }

    /**
     * Pantalla 4 - Informe de resultados.
     */
    public function mostrar(Request $request, int $id): View|RedirectResponse
    {
        $resultado = ResultadoChaside::with('estudiante.colegio')->findOrFail($id);

        // Guard: el resultado debe pertenecer al estudiante en sesion.
        if ($request->session()->get('estudiante_id') !== $resultado->estudiante_id) {
            return redirect()->route('welcome');
        }

        $puntajes = $resultado->puntajes();
        $maximo = max(max($puntajes), 1); // evita division por cero

        return view('pages.resultado', [
            'resultado'   => $resultado,
            'estudiante'  => $resultado->estudiante,
            'puntajes'    => $puntajes,
            'maximo'      => $maximo,
            'areas'       => Chaside::AREAS,
            'ordenAreas'  => Chaside::ORDEN_AREAS,
            'principal'   => $resultado->area_principal,
            'secundaria'  => $resultado->area_secundaria,
        ]);
    }

    /**
     * Genera y descarga el informe del estudiante en PDF.
     */
    public function pdf(Request $request, int $id)
    {
        $resultado = ResultadoChaside::with('estudiante.colegio')->findOrFail($id);

        // Guard: el resultado debe pertenecer al estudiante en sesion.
        if ($request->session()->get('estudiante_id') !== $resultado->estudiante_id) {
            return redirect()->route('welcome');
        }

        $puntajes = $resultado->puntajes();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.v4_pro', [
            'resultado'  => $resultado,
            'estudiante' => $resultado->estudiante,
            'puntajes'   => $puntajes,
            'maximo'     => max(max($puntajes), 1),
            'areas'      => Chaside::AREAS,
            'ordenAreas' => Chaside::ORDEN_AREAS,
            'principal'  => $resultado->area_principal,
            'secundaria' => $resultado->area_secundaria,
        ])->setPaper('a4', 'portrait');

        $slug = \Illuminate\Support\Str::slug($resultado->estudiante->nombre_completo);

        return $pdf->download("informe-chaside-{$slug}.pdf");
    }
}
