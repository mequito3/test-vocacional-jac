@extends('layouts.admin')

@section('title', 'Panel Admin')

@section('content')

{{-- Encabezado --}}
<div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8"
     x-data="{open: {{ $errors->any() ? 'true' : 'false' }}}">

  <div>
    <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400 mb-1">Panel de Administración</p>
    <h1 class="font-display font-extrabold text-ink text-3xl sm:text-4xl">Colegios</h1>
    <p class="text-slate-500 text-sm mt-1">
      {{ $colegios->count() }} {{ $colegios->count() === 1 ? 'colegio' : 'colegios' }} con estudiantes
      · <span class="font-semibold text-ink">{{ $colegios->sum('estudiantes_count') }}</span> registros totales
    </p>
  </div>

  {{-- Botón agregar (para generar enlace de grupo) --}}
  <div class="relative shrink-0">
    <button @click="open = !open"
            class="inline-flex items-center gap-2 rounded-2xl border border-slate-300 hover:border-navy-500 hover:bg-navy-50 text-slate-600 hover:text-navy-700 font-semibold text-sm px-5 py-3 transition-all">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"/>
      </svg>
      Crear enlace de grupo
    </button>

    <div x-show="open" x-transition x-cloak
         @click.outside="open = false"
         class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-slate-200 p-5 z-10">
      <p class="text-sm font-semibold text-ink mb-3">Nuevo enlace de grupo</p>
      @if (session('success'))
        <p class="text-emerald-700 bg-emerald-50 rounded-xl px-3 py-2 text-sm mb-3">{{ session('success') }}</p>
      @endif
      @error('nombre')
        <p class="text-rose-600 text-sm mb-2">{{ $message }}</p>
      @enderror
      <form method="POST" action="{{ route('admin.colegios.crear') }}">
        @csrf
        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Nombre del colegio</label>
        <input name="nombre" value="{{ old('nombre') }}" required autofocus
               placeholder="Ej: Unidad Educativa Sucre"
               class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-ink focus:border-navy-500 focus:ring-4 focus:ring-navy-500/10 outline-none transition @error('nombre') border-rose-400 @enderror">
        <p class="text-xs text-slate-400 mt-1.5 mb-3">Se generará un enlace único para enviar a ese colegio.</p>
        <div class="flex gap-2">
          <button type="submit" class="flex-1 rounded-xl bg-navy-700 hover:bg-navy-600 text-white font-semibold py-2.5 text-sm transition-colors">
            Generar enlace
          </button>
          <button type="button" @click="open = false" class="flex-1 rounded-xl border border-slate-200 text-slate-600 font-semibold py-2.5 text-sm hover:bg-slate-50 transition-colors">
            Cancelar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@if (session('success') && !$errors->any())
  <div class="mb-6 rounded-2xl bg-emerald-50 border border-emerald-200 px-5 py-3.5 text-sm text-emerald-700 font-medium flex items-center gap-2">
    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
    {{ session('success') }}
  </div>
@endif

{{-- Lista de colegios --}}
@if ($colegios->isEmpty())
  <div class="rounded-3xl border-2 border-dashed border-slate-200 py-16 text-center">
    <div class="w-16 h-16 rounded-2xl bg-slate-100 grid place-items-center mx-auto mb-4">
      <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21"/>
      </svg>
    </div>
    <p class="font-semibold text-slate-500">Aún no hay estudiantes registrados.</p>
    <p class="text-sm text-slate-400 mt-1">Los colegios aparecerán aquí cuando los alumnos completen su ficha.</p>
  </div>

@else
  {{-- Grid de colegios --}}
  <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach ($colegios as $col)
    <a href="{{ route('admin.colegios.ver', $col->id) }}"
       class="group bg-white rounded-3xl border border-slate-200 shadow-sm hover:shadow-lg hover:border-navy-300 hover:-translate-y-0.5 transition-all p-6 flex flex-col gap-4">

      {{-- Ícono + nombre --}}
      <div class="flex items-start gap-3">
        <div class="grid place-items-center w-11 h-11 rounded-2xl bg-navy-50 text-navy-600 group-hover:bg-navy-700 group-hover:text-white transition-colors shrink-0 mt-0.5">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21"/>
          </svg>
        </div>
        <div class="min-w-0 flex-1">
          <h2 class="font-display font-bold text-ink text-base leading-snug group-hover:text-navy-700 transition-colors line-clamp-2">
            {{ $col->nombre }}
          </h2>
        </div>
      </div>

      {{-- Contador + flecha --}}
      <div class="flex items-center justify-between mt-auto pt-3 border-t border-slate-100">
        <div class="flex items-center gap-1.5">
          <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
          </svg>
          <span class="text-sm text-slate-600">
            <b class="text-ink font-semibold">{{ $col->estudiantes_count }}</b>
            {{ $col->estudiantes_count === 1 ? 'estudiante' : 'estudiantes' }}
          </span>
        </div>
        <svg class="w-4 h-4 text-slate-300 group-hover:text-navy-500 group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
        </svg>
      </div>
    </a>
    @endforeach
  </div>
@endif

@endsection
