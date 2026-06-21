@extends('layouts.admin')

@section('title', 'Panel Admin')

@section('content')

{{-- Encabezado + crear colegio --}}
<div x-data="nuevoColegio({{ Illuminate\Support\Js::from($todosColegios) }})" class="mb-6">
  <div class="flex items-center justify-between gap-4 mb-1">
    <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400">Panel de Administración</p>
  </div>
  <div class="flex items-end justify-between gap-4">
    <div>
      <h1 class="font-display font-extrabold text-ink text-2xl sm:text-3xl leading-tight">Colegios</h1>
      <p class="text-slate-400 text-sm mt-0.5">
        {{ $colegios->count() }} {{ $colegios->count() === 1 ? 'colegio' : 'colegios' }} con estudiantes
        · <span class="font-semibold text-slate-600">{{ $colegios->sum('estudiantes_count') }}</span> registros
      </p>
    </div>
    <button @click="abierto = !abierto; if (!abierto) cerrar()"
            :class="abierto ? 'bg-slate-100 text-slate-500' : 'bg-navy-700 hover:bg-navy-600 text-white shadow-sm'"
            class="shrink-0 inline-flex items-center gap-1.5 rounded-xl font-semibold text-sm px-4 py-2.5 transition-all">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" :d="abierto ? 'M6 18L18 6M6 6l12 12' : 'M12 4v16m8-8H4'"/>
      </svg>
      <span x-text="abierto ? 'Cancelar' : 'Nuevo colegio'"></span>
    </button>
  </div>

  {{-- Formulario crear colegio --}}
  <div x-show="abierto" x-cloak
       x-transition:enter="transition ease-out duration-200"
       x-transition:enter-start="opacity-0 -translate-y-2"
       x-transition:enter-end="opacity-100 translate-y-0"
       x-transition:leave="transition ease-in duration-150"
       x-transition:leave-start="opacity-100 translate-y-0"
       x-transition:leave-end="opacity-0 -translate-y-2"
       class="mt-5">
    <form method="POST" action="{{ route('admin.colegios.crear') }}"
          class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex flex-col sm:flex-row gap-3">
      @csrf
      <div class="relative flex-1">
        <input type="text" name="nombre" required
               x-model="query"
               @input="buscar()"
               @focus="buscar()"
               @keydown.arrow-down.prevent="bajar()"
               @keydown.arrow-up.prevent="subir()"
               @keydown.enter.prevent="seleccionar()"
               @keydown.escape="cerrar()"
               @blur="cerrarDelay()"
               placeholder="Nombre del colegio o unidad educativa…"
               autocomplete="off"
               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-ink placeholder:text-slate-400 focus:border-navy-500 focus:ring-4 focus:ring-navy-500/10 outline-none transition text-sm">

        {{-- Dropdown sugerencias --}}
        <div x-show="sugerencias.length > 0" x-cloak
             class="absolute z-50 w-full mt-1.5 bg-white rounded-2xl border border-slate-200 shadow-xl overflow-hidden max-h-56 overflow-y-auto">
          <p class="px-4 pt-3 pb-1 text-[10px] font-bold uppercase tracking-widest text-slate-400">Ya existe — haz clic para ir</p>
          <template x-for="(c, i) in sugerencias" :key="c.id">
            <button type="button"
                    @mousedown.prevent="irA(c.url)"
                    class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-left transition-colors"
                    :class="i === activo ? 'bg-navy-700 text-white' : 'text-slate-700 hover:bg-slate-50'">
              <svg class="w-4 h-4 shrink-0 opacity-50" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21"/>
              </svg>
              <span x-text="c.nombre" class="flex-1 truncate"></span>
              <svg class="w-3.5 h-3.5 shrink-0 opacity-50" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
              </svg>
            </button>
          </template>
        </div>
      </div>

      <button type="submit"
              class="shrink-0 rounded-xl bg-navy-700 hover:bg-navy-600 text-white font-semibold text-sm px-6 py-3 transition-all">
        Crear colegio
      </button>
    </form>
    <p class="text-xs text-slate-400 mt-2 ml-1">Si el colegio ya existe aparecerá en la lista — haz clic para ir a él. Si es nuevo, escribe el nombre y crea.</p>
  </div>
</div>

{{-- Stats globales --}}
<div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-6">
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 flex items-center gap-3">
    <div class="grid place-items-center w-9 h-9 rounded-xl bg-navy-50 text-navy-600 shrink-0">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
      </svg>
    </div>
    <div>
      <p class="font-display font-extrabold text-xl text-ink leading-none">{{ $stats['total'] }}</p>
      <p class="text-[11px] text-slate-400 mt-0.5">registros</p>
    </div>
  </div>
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 flex items-center gap-3">
    <div class="grid place-items-center w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 shrink-0">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
    </div>
    <div>
      <p class="font-display font-extrabold text-xl text-ink leading-none">{{ $stats['completados'] }}</p>
      <p class="text-[11px] text-slate-400 mt-0.5">completaron</p>
    </div>
  </div>
  <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 flex items-center gap-3 col-span-2 sm:col-span-1">
    <div class="grid place-items-center w-9 h-9 rounded-xl shrink-0 {{ $stats['hoy'] > 0 ? 'bg-amber-50 text-amber-500' : 'bg-slate-50 text-slate-400' }}">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
    </div>
    <div>
      <p class="font-display font-extrabold text-xl leading-none {{ $stats['hoy'] > 0 ? 'text-amber-500' : 'text-ink' }}">{{ $stats['hoy'] }}</p>
      <p class="text-[11px] text-slate-400 mt-0.5">nuevos hoy</p>
    </div>
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
  {{-- Buscador --}}
  <div x-data="{q: ''}">
    <div class="mb-4">
      <div class="relative w-full sm:w-80">
        <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
        </svg>
        <input type="text" x-model="q" placeholder="Buscar colegio..."
               class="w-full rounded-xl border border-slate-200 bg-white shadow-sm pl-10 pr-4 py-2.5 text-sm text-ink placeholder:text-slate-400 focus:border-navy-400 focus:ring-4 focus:ring-navy-500/10 outline-none transition">
      </div>
    </div>

    {{-- Grid de colegios --}}
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
      @foreach ($colegios as $col)
      <div class="relative" x-data="{confirmar: false, menu: false}"
           x-show="!q || '{{ addslashes(mb_strtolower($col->nombre)) }}'.includes(q.toLowerCase())">

        {{-- Card principal --}}
        <a href="{{ route('admin.colegios.ver', $col->id) }}"
           :class="confirmar ? 'pointer-events-none opacity-30' : ''"
           class="group bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md hover:border-navy-200 hover:-translate-y-0.5 transition-all p-4 flex flex-col gap-3 min-h-0">

          {{-- Ícono + nombre + menú en misma fila --}}
          <div class="flex items-start gap-3 pr-7">
            <div class="grid place-items-center w-8 h-8 rounded-lg bg-slate-50 text-slate-400 group-hover:bg-navy-700 group-hover:text-white transition-colors shrink-0 mt-0.5">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21"/>
              </svg>
            </div>
            <h2 class="font-semibold text-ink text-sm leading-snug group-hover:text-navy-700 transition-colors line-clamp-2 flex-1">
              {{ $col->nombre }}
            </h2>
          </div>

          <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between gap-2">
            <div>
              <p class="text-sm font-semibold text-ink">
                {{ $col->estudiantes_count }}
                <span class="font-normal text-slate-500">{{ $col->estudiantes_count === 1 ? 'estudiante' : 'estudiantes' }}</span>
              </p>
              @if ($col->estudiantes_max_created_at)
                <p class="text-[11px] text-slate-400 mt-0.5">
                  Último: {{ \Carbon\Carbon::parse($col->estudiantes_max_created_at)->diffForHumans() }}
                </p>
              @endif
            </div>
            <span class="text-[11px] font-semibold text-slate-400 group-hover:text-navy-600 transition-colors whitespace-nowrap">Ver →</span>
          </div>
        </a>

        {{-- Menú tres puntos --}}
        <div class="absolute top-3 right-3">
          <button @click.prevent.stop="menu = !menu"
                  @click.outside="menu = false"
                  title="Opciones"
                  class="grid place-items-center w-8 h-8 rounded-xl text-slate-300 hover:bg-slate-100 hover:text-slate-500 transition-all">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 6a1.5 1.5 0 100-3 1.5 1.5 0 000 3zm0 7.5a1.5 1.5 0 100-3 1.5 1.5 0 000 3zm0 7.5a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"/>
            </svg>
          </button>
          <div x-show="menu" x-cloak x-transition
               class="absolute right-0 mt-1 w-36 bg-white rounded-2xl border border-slate-200 shadow-xl py-1.5 z-10">
            <button type="button"
                    @click.prevent.stop="confirmar = true; menu = false"
                    class="flex items-center gap-2.5 w-full px-4 py-2.5 text-sm text-rose-600 hover:bg-rose-50 transition-colors">
              <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
              </svg>
              Eliminar
            </button>
          </div>
        </div>

      {{-- Confirmación eliminar — modal centrado --}}
      <div x-show="confirmar" x-cloak
           x-transition:enter="transition ease-out duration-200"
           x-transition:enter-start="opacity-0"
           x-transition:enter-end="opacity-100"
           x-transition:leave="transition ease-in duration-150"
           x-transition:leave-start="opacity-100"
           x-transition:leave-end="opacity-0"
           class="fixed inset-0 z-50 flex items-center justify-center p-4">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-ink/40 backdrop-blur-sm" @click="confirmar = false"></div>

        {{-- Card --}}
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-sm p-7 flex flex-col gap-5"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-2">

          <div class="text-center">
            <div class="grid place-items-center w-14 h-14 rounded-2xl bg-rose-50 mx-auto mb-4">
              <svg class="w-7 h-7 text-rose-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
              </svg>
            </div>
            <h3 class="font-display font-bold text-ink text-lg">¿Eliminar este colegio?</h3>
            <p class="text-sm text-slate-500 mt-1">{{ $col->nombre }}</p>
            <p class="text-sm text-rose-500 font-medium mt-2">Esta acción no se puede deshacer.</p>
          </div>

          <div class="flex gap-3">
            <button type="button" @click="confirmar = false"
                    class="flex-1 rounded-xl border border-slate-200 text-slate-600 font-semibold text-sm py-3 hover:bg-slate-50 transition-colors">
              Cancelar
            </button>
            <form method="POST" action="{{ route('admin.colegios.eliminar', $col->id) }}" class="flex-1">
              @csrf
              @method('DELETE')
              <button type="submit"
                      class="w-full rounded-xl bg-rose-500 hover:bg-rose-600 text-white font-semibold text-sm py-3 transition-colors">
                Sí, eliminar
              </button>
            </form>
          </div>
        </div>
      </div>

    </div>
      @endforeach
    </div>
  </div>{{-- fin buscador x-data --}}
@endif

@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('nuevoColegio', (colegios) => ({
        abierto:    false,
        query:      '',
        sugerencias:[],
        activo:     -1,

        normalizar(s) {
            return s.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '');
        },

        buscar() {
            if (this.query.length < 2) { this.sugerencias = []; return; }
            const q = this.normalizar(this.query);
            this.sugerencias = colegios
                .filter(c => this.normalizar(c.nombre).includes(q))
                .slice(0, 8);
            this.activo = -1;
        },

        bajar() {
            this.activo = Math.min(this.activo + 1, this.sugerencias.length - 1);
        },

        subir() {
            this.activo = Math.max(this.activo - 1, 0);
        },

        seleccionar() {
            if (this.activo >= 0 && this.activo < this.sugerencias.length) {
                this.irA(this.sugerencias[this.activo].url);
            }
        },

        irA(url) {
            window.location.href = url;
        },

        cerrar() {
            this.sugerencias = [];
            this.activo = -1;
        },

        cerrarDelay() {
            setTimeout(() => this.cerrar(), 160);
        },
    }));
});
</script>
@endpush
