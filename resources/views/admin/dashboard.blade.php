@extends('layouts.admin')

@section('title', 'Panel Admin')

@section('content')

{{-- Encabezado --}}
<div class="mb-8">
  <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400 mb-1">Panel de Administración</p>
  <h1 class="font-display font-extrabold text-ink text-3xl sm:text-4xl">Colegios</h1>
  <p class="text-slate-500 text-sm mt-1">
    {{ $colegios->count() }} {{ $colegios->count() === 1 ? 'colegio' : 'colegios' }} con estudiantes
    · <span class="font-semibold text-ink">{{ $colegios->sum('estudiantes_count') }}</span> registros totales
  </p>
</div>

{{-- Stats globales --}}
<div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-8">
  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
    <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-slate-400 mb-1">Total registros</p>
    <p class="font-display font-extrabold text-3xl text-ink">{{ $stats['total'] }}</p>
    <p class="text-xs text-slate-400 mt-0.5">estudiantes</p>
  </div>
  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
    <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-slate-400 mb-1">Completaron</p>
    <p class="font-display font-extrabold text-3xl text-ink">{{ $stats['completados'] }}</p>
    <p class="text-xs text-slate-400 mt-0.5">con resultado</p>
  </div>
  <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
    <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-slate-400 mb-1">Hoy</p>
    <p class="font-display font-extrabold text-3xl {{ $stats['hoy'] > 0 ? 'text-emerald-600' : 'text-ink' }}">{{ $stats['hoy'] }}</p>
    <p class="text-xs text-slate-400 mt-0.5">nuevos hoy</p>
  </div>
</div>

@if (session('success'))
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
    <div class="relative" x-data="{confirmar: false}">

      {{-- Card principal --}}
      <a href="{{ route('admin.colegios.ver', $col->id) }}"
         :class="confirmar ? 'pointer-events-none opacity-30' : ''"
         class="group bg-white rounded-3xl border border-slate-200 shadow-sm hover:shadow-lg hover:border-navy-300 hover:-translate-y-0.5 transition-all p-6 flex flex-col gap-4">

        {{-- Ícono + nombre --}}
        <div class="flex items-start gap-3 pr-8">
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

      {{-- Botón eliminar --}}
      <button @click.prevent.stop="confirmar = true"
              x-show="!confirmar"
              title="Eliminar colegio"
              class="absolute top-3 right-3 grid place-items-center w-8 h-8 rounded-xl text-slate-300 hover:bg-rose-50 hover:text-rose-500 transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
        </svg>
      </button>

      {{-- Confirmación eliminar --}}
      <div x-show="confirmar" x-cloak x-transition
           class="absolute inset-0 bg-white/95 backdrop-blur-sm rounded-3xl border-2 border-rose-200 flex flex-col items-center justify-center gap-4 p-6 z-10">
        <div class="text-center">
          <div class="grid place-items-center w-12 h-12 rounded-2xl bg-rose-50 mx-auto mb-3">
            <svg class="w-6 h-6 text-rose-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
            </svg>
          </div>
          <p class="font-semibold text-ink text-sm">¿Eliminar este colegio?</p>
          <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $col->nombre }}</p>
          <p class="text-xs text-rose-500 mt-1">Esta acción no se puede deshacer.</p>
        </div>
        <div class="flex gap-2 w-full">
          <form method="POST" action="{{ route('admin.colegios.eliminar', $col->id) }}" class="flex-1">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="w-full rounded-xl bg-rose-500 hover:bg-rose-600 text-white font-semibold text-sm py-2.5 transition-colors">
              Sí, eliminar
            </button>
          </form>
          <button type="button" @click="confirmar = false"
                  class="flex-1 rounded-xl border border-slate-200 text-slate-600 font-semibold text-sm py-2.5 hover:bg-slate-50 transition-colors">
            Cancelar
          </button>
        </div>
      </div>

    </div>
    @endforeach
  </div>
@endif

@endsection
