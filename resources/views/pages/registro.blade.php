@extends('layouts.app')

@section('title', 'Tu ficha')

@php
  $inputCls = 'w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-3.5 text-ink placeholder:text-slate-400 focus:border-navy-500 focus:ring-4 focus:ring-navy-500/12 outline-none transition';
@endphp

@section('content')
<x-container size="form" class="pt-10 pb-4">

  {{-- Stepper --}}
  <div class="rise d1 flex items-center justify-center gap-2 sm:gap-3 mb-7">
    @php $steps=[['1','Ficha',true],['2','Test',false],['3','Resultado',false]]; @endphp
    @foreach ($steps as $i => [$n,$lbl,$active])
      <div class="flex items-center gap-2">
        <span class="grid place-items-center w-8 h-8 rounded-full text-sm font-bold {{ $active ? 'bg-navy-700 text-white shadow-btn' : 'bg-white text-slate-400 border border-slate-200' }}">{{ $n }}</span>
        <span class="text-sm font-semibold {{ $active ? 'text-ink' : 'text-slate-400' }} hidden sm:inline">{{ $lbl }}</span>
      </div>
      @if (!$loop->last)<span class="w-6 sm:w-10 h-0.5 rounded bg-slate-200"></span>@endif
    @endforeach
  </div>

  <div class="rise d2 text-center mb-7">
    <h1 class="font-display font-extrabold text-ink text-4xl sm:text-5xl tracking-tight">Cuéntanos sobre ti</h1>
    <p class="text-slate-500 mt-2">Tus datos quedan solo con tu informe. Los campos con <span class="text-rose-500 font-semibold">*</span> son obligatorios.</p>
  </div>

  @if ($errors->any())
    <div class="rise d2 mb-6 rounded-2xl border border-rose-200 bg-rose-50/80 px-5 py-4 text-sm text-rose-700">
      <p class="font-semibold mb-1 flex items-center gap-2">
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9 9a1 1 0 012 0v4a1 1 0 11-2 0V9zm1-5a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"/></svg>
        Revisa estos campos:
      </p>
      <ul class="list-disc list-inside space-y-0.5 ml-1">
        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('registro.guardar') }}" class="space-y-5">
    @csrf

    {{-- Datos del estudiante --}}
    <div class="rise d3 card rounded-3xl shadow-card p-6 sm:p-7">
      <div class="flex items-center gap-3 mb-5">
        <span class="grid place-items-center w-10 h-10 rounded-xl bg-navy-700/10 text-navy-700">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        </span>
        <h2 class="font-display font-bold text-ink text-lg">Datos del estudiante</h2>
      </div>
      <div class="grid sm:grid-cols-2 gap-4">

        {{-- Colegio (campo completo, ocupa ambas columnas) --}}
        <div class="sm:col-span-2">
          <label class="block text-sm font-semibold text-slate-600 mb-1.5">
            Colegio / Unidad Educativa <span class="text-rose-500">*</span>
          </label>
          @if ($colegioSesion)
            {{-- Vino por enlace de grupo: colegio ya fijado --}}
            <div class="flex items-center gap-3 rounded-2xl border border-navy-200 bg-navy-50 px-4 py-3.5">
              <svg class="w-4 h-4 text-navy-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <span class="font-semibold text-navy-700 text-sm">{{ $colegioSesion->nombre }}</span>
              <span class="text-xs text-navy-500 ml-auto">Asignado por enlace</span>
            </div>
            <input type="hidden" name="colegio_nombre" value="{{ $colegioSesion->nombre }}">
          @else
            {{-- Acceso directo: autocomplete personalizado con Alpine.js --}}
            <div x-data="autocompleteColegio({{ Illuminate\Support\Js::from($colegios) }}, {{ Illuminate\Support\Js::from(old('colegio_nombre', '')) }})"
                 class="relative">

              {{-- Input visible --}}
              <div class="relative">
                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none"
                     fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21"/>
                </svg>
                <input
                  name="colegio_nombre"
                  x-model="query"
                  @input="buscar()"
                  @focus="abrirSi()"
                  @keydown.arrow-down.prevent="bajar()"
                  @keydown.arrow-up.prevent="subir()"
                  @keydown.enter.prevent="seleccionar()"
                  @keydown.escape="cerrar()"
                  @blur="cerrarDelay()"
                  required
                  autocomplete="off"
                  placeholder="Escribe el nombre de tu colegio o unidad educativa…"
                  class="{{ $inputCls }} pl-10 @error('colegio_nombre') border-rose-400 @enderror">

                {{-- Limpiar --}}
                <button type="button" x-show="query.length > 0" @click="query = ''; resultados = []; abierto = false"
                        class="absolute right-3.5 top-1/2 -translate-y-1/2 w-5 h-5 grid place-items-center text-slate-400 hover:text-slate-600 transition-colors">
                  <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                </button>
              </div>

              {{-- Dropdown --}}
              <div x-show="abierto"
                   x-transition:enter="transition ease-out duration-100"
                   x-transition:enter-start="opacity-0 -translate-y-1 scale-[.98]"
                   x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                   x-transition:leave="transition ease-in duration-75"
                   x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                   x-transition:leave-end="opacity-0 -translate-y-1 scale-[.98]"
                   x-cloak
                   class="absolute z-50 w-full mt-2 bg-white rounded-2xl border border-slate-200/80 shadow-[0_20px_60px_-15px_rgba(27,58,107,.25)] overflow-hidden max-h-64 overflow-y-auto">

                <template x-for="(item, i) in resultados" :key="i">
                  <button type="button"
                          :data-idx="i"
                          @mousedown.prevent="elegir(item)"
                          class="w-full text-left px-4 py-3 text-sm flex items-center gap-3 transition-colors"
                          :class="i === activo
                            ? 'bg-navy-700 text-white'
                            : 'text-slate-700 hover:bg-navy-50 hover:text-navy-700'">
                    <svg class="w-4 h-4 shrink-0 opacity-50" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75"/>
                    </svg>
                    <span x-html="resaltar(item, i === activo)"></span>
                  </button>
                </template>

                {{-- Ningún resultado: el texto se guardará como colegio nuevo --}}
                <div x-show="resultados.length === 0 && query.length >= 2"
                     class="px-4 py-3.5 flex items-center gap-3 text-sm text-slate-500">
                  <svg class="w-4 h-4 shrink-0 text-gold-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                  </svg>
                  No está en la lista — se registrará como nuevo colegio.
                </div>
              </div>
            </div>

            <p class="text-xs text-slate-400 mt-1.5">
              Escribe 2 o más letras y selecciona de la lista.
            </p>
          @endif
          @error('colegio_nombre')
            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <label class="block text-sm font-semibold text-slate-600 mb-1.5">Nombre <span class="text-rose-500">*</span></label>
          <input name="nombre" value="{{ old('nombre') }}" required placeholder="Erika" class="{{ $inputCls }} @error('nombre') border-rose-400 @enderror">
        </div>
        <div>
          <label class="block text-sm font-semibold text-slate-600 mb-1.5">Apellido <span class="text-rose-500">*</span></label>
          <input name="apellido" value="{{ old('apellido') }}" required placeholder="Pérez Calvimontes" class="{{ $inputCls }} @error('apellido') border-rose-400 @enderror">
        </div>
        <div>
          <label class="block text-sm font-semibold text-slate-600 mb-1.5">Sexo <span class="text-rose-500">*</span></label>
          <select name="sexo" required class="{{ $inputCls }} @error('sexo') border-rose-400 @enderror">
            <option value="">Indicar…</option>
            @foreach (['Femenino','Masculino','Otro'] as $op)
              <option value="{{ $op }}" @selected(old('sexo') === $op)>{{ $op }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-sm font-semibold text-slate-600 mb-1.5">Edad <span class="text-rose-500">*</span></label>
          <input type="number" name="edad" value="{{ old('edad') }}" min="12" max="35" required placeholder="18" class="{{ $inputCls }} @error('edad') border-rose-400 @enderror">
        </div>
        <div>
          <label class="block text-sm font-semibold text-slate-600 mb-1.5">Celular <span class="text-rose-500">*</span></label>
          <input name="celular" value="{{ old('celular') }}" required placeholder="Ej: 76543210" class="{{ $inputCls }} @error('celular') border-rose-400 @enderror">
          @error('celular')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="block text-sm font-semibold text-slate-600 mb-1.5">Correo electrónico</label>
          <input type="email" name="email" value="{{ old('email') }}" placeholder="nombre@correo.com" class="{{ $inputCls }} @error('email') border-rose-400 @enderror">
        </div>
      </div>
    </div>

    {{-- Datos de los padres --}}
    <div class="rise d4 card rounded-3xl shadow-card p-6 sm:p-7">
      <div class="flex items-center gap-3 mb-5">
        <span class="grid place-items-center w-10 h-10 rounded-xl bg-gold-500/15 text-gold-600">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-1a4 4 0 00-3-3.87M9 20H4v-1a4 4 0 013-3.87m6-1.13a4 4 0 10-4 0M19 8a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </span>
        <h2 class="font-display font-bold text-ink text-lg">Datos de los padres <span class="text-sm font-normal text-slate-400">(opcional)</span></h2>
      </div>
      <div class="grid sm:grid-cols-2 gap-4">
        <div><label class="block text-sm font-semibold text-slate-600 mb-1.5">Nombre de la madre</label><input name="nombre_madre" value="{{ old('nombre_madre') }}" placeholder="Nombre completo" class="{{ $inputCls }}"></div>
        <div><label class="block text-sm font-semibold text-slate-600 mb-1.5">Celular de la madre</label><input name="celular_madre" value="{{ old('celular_madre') }}" placeholder="+591 7XXXXXXX" class="{{ $inputCls }}"></div>
        <div><label class="block text-sm font-semibold text-slate-600 mb-1.5">Nombre del padre</label><input name="nombre_padre" value="{{ old('nombre_padre') }}" placeholder="Nombre completo" class="{{ $inputCls }}"></div>
        <div><label class="block text-sm font-semibold text-slate-600 mb-1.5">Celular del padre</label><input name="celular_padre" value="{{ old('celular_padre') }}" placeholder="+591 7XXXXXXX" class="{{ $inputCls }}"></div>
      </div>
    </div>

    <div class="rise d5 flex items-center justify-between gap-4 pt-1">
      <a href="{{ route('welcome') }}" class="text-slate-500 hover:text-ink font-semibold text-sm">← Volver</a>
      <button type="submit" class="group inline-flex items-center gap-2.5 rounded-2xl bg-navy-700 hover:bg-navy-600 text-white font-semibold px-8 py-4 shadow-btn hover:-translate-y-0.5 transition-all">
        Comenzar el test
        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
      </button>
    </div>
  </form>
</x-container>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('autocompleteColegio', (lista, valorInicial) => ({
        lista,
        query:     valorInicial || '',
        resultados:[],
        abierto:   false,
        activo:    -1,

        init() {
            // Si hay un valor previo (error de validación), muestra sugerencias
            if (this.query.length >= 2) this.buscar();
        },

        normalizar(s) {
            return s.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '');
        },

        buscar() {
            if (this.query.length < 2) {
                this.resultados = [];
                this.abierto    = false;
                return;
            }
            const q = this.normalizar(this.query);
            this.resultados = this.lista
                .filter(n => this.normalizar(n).includes(q))
                .slice(0, 9);
            this.abierto = true;
            this.activo  = -1;
        },

        abrirSi() {
            if (this.query.length >= 2) this.buscar();
        },

        elegir(nombre) {
            this.query   = nombre;
            this.abierto = false;
            this.activo  = -1;
        },

        bajar() {
            if (!this.abierto) return;
            this.activo = Math.min(this.activo + 1, this.resultados.length - 1);
            this.$nextTick(() => this.scrollActivo());
        },

        subir() {
            if (!this.abierto) return;
            this.activo = Math.max(this.activo - 1, 0);
            this.$nextTick(() => this.scrollActivo());
        },

        scrollActivo() {
            const btn = this.$el.querySelector(`[data-idx="${this.activo}"]`);
            if (btn) btn.scrollIntoView({ block: 'nearest' });
        },

        seleccionar() {
            if (this.activo >= 0 && this.activo < this.resultados.length) {
                this.elegir(this.resultados[this.activo]);
            }
        },

        cerrar() {
            this.abierto = false;
            this.activo  = -1;
        },

        cerrarDelay() {
            // Espera para que mousedown alcance a disparar antes del blur
            setTimeout(() => this.cerrar(), 160);
        },

        resaltar(texto, isActivo) {
            if (this.query.length < 2) return texto;
            try {
                const escaped = this.query.trim().replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                const repl = isActivo
                    ? '<strong>$1</strong>'
                    : '<mark style="background:rgba(245,166,35,.35);border-radius:3px;padding:0 2px;color:inherit">$1</mark>';
                return texto.replace(new RegExp(`(${escaped})`, 'gi'), repl);
            } catch (e) {
                return texto;
            }
        },
    }));
});
</script>
@endpush
