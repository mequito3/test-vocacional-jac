<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegistroRequest;
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
    public function registro(): View
    {
        return view('pages.registro');
    }

    /**
     * Guarda la ficha, deja el estudiante en sesion y avanza al test.
     */
    public function guardarRegistro(RegistroRequest $request): RedirectResponse
    {
        $estudiante = Estudiante::create($request->validated());

        $request->session()->put('estudiante_id', $estudiante->id);

        return redirect()->route('test');
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
            'preguntas' => Chaside::PREGUNTAS,
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
