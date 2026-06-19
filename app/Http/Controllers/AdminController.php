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

        if ($request->input('password') === config('app.admin_password')) {
            $request->session()->regenerate();
            $request->session()->put('admin_authed', true);
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['password' => 'Contraseña incorrecta.']);
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('admin_authed');
        return redirect()->route('admin.login');
    }

    public function dashboard(): View
    {
        $colegios = Colegio::withCount('estudiantes')
            ->having('estudiantes_count', '>', 0)
            ->orderByDesc('estudiantes_count')
            ->orderBy('nombre')
            ->get();

        $stats = [
            'total'       => Estudiante::count(),
            'completados' => ResultadoChaside::distinct('estudiante_id')->count('estudiante_id'),
            'hoy'         => Estudiante::whereDate('created_at', today())->count(),
        ];

        return view('admin.dashboard', compact('colegios', 'stats'));
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
