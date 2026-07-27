<?php

namespace App\Http\Controllers;

use App\Models\Colegio;
use App\Models\Estudiante;
use App\Models\ResultadoChaside;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function loginForm(): View
    {
        return view('admin.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate(['password' => 'required']);

        $configuredPassword = config('app.admin_password');
        $submittedPassword = (string) $request->input('password');

        if (is_string($configuredPassword)
            && $configuredPassword !== ''
            && hash_equals($configuredPassword, $submittedPassword)) {
            $request->session()->regenerate();
            $request->session()->put('admin_authed', true);
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['password' => 'Contraseña incorrecta.']);
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    public function dashboard(): View
    {
        $colegios = Colegio::whereHas('estudiantes')
            ->withCount([
                'estudiantes',
                'estudiantes as completados_count' => fn($q) => $q->whereHas('resultados'),
            ])
            ->withMax('estudiantes', 'created_at')
            ->orderByDesc('estudiantes_count')
            ->orderBy('nombre')
            ->get();

        $sinColegio = [
            'total'       => Estudiante::whereNull('colegio_id')->count(),
            'completados' => Estudiante::whereNull('colegio_id')->whereHas('resultados')->count(),
            'ultimo'      => Estudiante::whereNull('colegio_id')->max('created_at'),
        ];

        $stats = [
            'total'       => Estudiante::count(),
            'completados' => ResultadoChaside::distinct('estudiante_id')->count('estudiante_id'),
            'hoy'         => Estudiante::whereDate('created_at', today())->count(),
        ];

        $todosColegios = Colegio::orderBy('nombre')
            ->get(['id', 'nombre'])
            ->map(fn($c) => [
                'id'     => $c->id,
                'nombre' => $c->nombre,
                'url'    => route('admin.colegios.ver', $c->id),
            ]);

        return view('admin.dashboard', compact('colegios', 'stats', 'todosColegios', 'sinColegio'));
    }

    public function crearColegio(Request $request): RedirectResponse
    {
        $request->validate(['nombre' => 'required|string|min:2|max:150']);

        $nombre = trim((string) $request->input('nombre'));
        $colegio = Colegio::firstOrCreate(['nombre' => $nombre]);

        $mensaje = $colegio->wasRecentlyCreated
            ? 'Colegio creado correctamente. Ya puedes copiar su enlace de registro.'
            : 'El colegio ya existia. Te lleve a su enlace de registro.';

        return redirect()
            ->route('admin.colegios.ver', $colegio->id)
            ->with('success', $mensaje);
    }

    public function eliminarColegio(Request $request, int $id): RedirectResponse
    {
        Colegio::findOrFail($id)->delete();
        return back()->with('success', 'Colegio eliminado.');
    }

    public function verColegio(int $id): View
    {
        $colegio = Colegio::findOrFail($id);
        $estudiantes = Estudiante::where('colegio_id', $id)
            ->with(['resultados' => fn($q) => $q->latest()])
            ->latest()
            ->get();

        return view('admin.colegio', compact('colegio', 'estudiantes'));
    }

    public function recuentoColegio(int $id): \Illuminate\Http\JsonResponse
    {
        $count = Estudiante::where('colegio_id', $id)
            ->whereHas('resultados')
            ->count();
        return response()->json(['count' => $count]);
    }

    public function sinColegio(): View
    {
        $estudiantes = Estudiante::whereNull('colegio_id')
            ->with(['resultados' => fn($q) => $q->latest()])
            ->latest()
            ->get();

        return view('admin.colegio', [
            'colegio'     => null,
            'estudiantes' => $estudiantes,
        ]);
    }
}
