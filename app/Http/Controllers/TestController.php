<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegistroRequest;
use App\Models\Colegio;
use App\Models\Estudiante;
use App\Support\Chaside;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TestController extends Controller
{
    /**
     * Pantalla 1 - Bienvenida.
     */
    public function welcome(): View
    {
        return view('pages.welcome');
    }

    /**
     * Pantalla 2 - Formulario de ficha del estudiante.
     */
    public function registro(Request $request): View
    {
        $colegios = Colegio::orderBy('nombre')->pluck('nombre');

        // Si viene por enlace de grupo, mostramos el colegio ya asignado
        $colegioSesion = $request->session()->has('colegio_id')
            ? Colegio::find($request->session()->get('colegio_id'))
            : null;

        return view('pages.registro', compact('colegios', 'colegioSesion'));
    }

    /**
     * Guarda la ficha, deja el estudiante en sesion y avanza al test.
     */
    public function guardarRegistro(RegistroRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->session()->has('colegio_id')) {
            // Vino por enlace de grupo: el colegio ya está fijado
            $data['colegio_id'] = $request->session()->get('colegio_id');
        } else {
            // Acceso directo: buscamos o creamos el colegio por nombre
            $colegio = Colegio::firstOrCreate([
                'nombre' => trim($data['colegio_nombre']),
            ]);
            $data['colegio_id'] = $colegio->id;
        }

        unset($data['colegio_nombre']); // no es columna de estudiantes
        $estudiante = Estudiante::create($data);

        $request->session()->put('estudiante_id', $estudiante->id);

        return redirect()->route('test');
    }

    /**
     * Enlace de grupo: guarda el colegio en sesion y redirige a bienvenida.
     */
    public function grupo(Request $request, string $token): RedirectResponse
    {
        $colegio = Colegio::where('token', $token)->firstOrFail();
        $request->session()->put('colegio_id', $colegio->id);
        return redirect()->route('welcome');
    }

    /**
     * Pantalla 3 - Cuestionario de 98 preguntas.
     */
    public function test(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has('estudiante_id')) {
            return redirect()->route('registro');
        }

        return view('pages.test', [
            'preguntas'    => Chaside::PREGUNTAS,
            'estudianteId' => $request->session()->get('estudiante_id'),
        ]);
    }

    /**
     * Limpia la sesion y vuelve al inicio para rendir otro test.
     */
    public function reiniciar(Request $request): RedirectResponse
    {
        $request->session()->forget('estudiante_id');

        return redirect()->route('welcome');
    }
}
