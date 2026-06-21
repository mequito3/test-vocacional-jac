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
        $colegios = Colegio::withCount([
            'estudiantes',
            'estudiantes as completados_count' => fn($q) => $q->whereHas('resultados'),
        ])
            ->withMax('estudiantes', 'created_at')
            ->having('estudiantes_count', '>', 0)
            ->orderByDesc('estudiantes_count')
            ->orderBy('nombre')
            ->get();

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

        return view('admin.dashboard', compact('colegios', 'stats', 'todosColegios'));
    }

    public function crearColegio(Request $request): RedirectResponse
    {
        $request->validate(['nombre' => 'required|string|min:2|max:150']);
        Colegio::create(['nombre' => trim($request->input('nombre'))]);
        return back()->with('success', 'Colegio creado correctamente.');
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
