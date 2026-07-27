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

  <form method="POST" action="{{ route('registro.guardar') }}" novalidate
        x-data="validarFicha('{{ old('sexo', '') }}')"
        @submit.prevent="enviar($el)"
        class="space-y-5">
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

        {{-- Colegio --}}
        @if ($colegioSesion)
        <div class="sm:col-span-2">
          <label class="block text-sm font-semibold text-slate-600 mb-1.5">Colegio / Unidad Educativa</label>
          <div class="flex items-center gap-3 rounded-2xl border border-navy-200 bg-navy-50 px-4 py-3.5">
            <svg class="w-4 h-4 text-navy-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="font-semibold text-navy-700 text-sm">{{ $colegioSesion->nombre }}</span>
            <span class="text-xs text-navy-500 ml-auto">Asignado por enlace</span>
          </div>
          <input type="hidden" name="colegio_nombre" value="{{ $colegioSesion->nombre }}">
        </div>
        @elseif ($mostrarColegio)
        <div class="sm:col-span-2">
          <label class="block text-sm font-semibold text-slate-600 mb-1.5">
            Colegio / Unidad Educativa <span class="text-rose-500">*</span>
          </label>
          <div x-data="autocompleteColegio({{ Illuminate\Support\Js::from($colegios) }}, {{ Illuminate\Support\Js::from(old('colegio_nombre', '')) }})"
               class="relative">
            <div class="relative">
              <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none"
                   fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21"/>
              </svg>
              <input name="colegio_nombre" x-model="query"
                     @input="buscar()" @focus="abrirSi()"
                     @keydown.arrow-down.prevent="bajar()" @keydown.arrow-up.prevent="subir()"
                     @keydown.enter.prevent="seleccionar()" @keydown.escape="cerrar()" @blur="cerrarDelay()"
                     autocomplete="off"
                     placeholder="Escribe el nombre de tu colegio o unidad educativa…"
                     class="{{ $inputCls }} pl-10 @error('colegio_nombre') border-rose-400 @enderror">
              <button type="button" x-show="query.length > 0" @click="query = ''; resultados = []; abierto = false"
                      class="absolute right-3.5 top-1/2 -translate-y-1/2 w-5 h-5 grid place-items-center text-slate-400 hover:text-slate-600 transition-colors">
                <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" class="w-4 h-4">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>
            <div x-show="abierto" x-cloak
                 x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 -translate-y-1 scale-[.98]" x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 -translate-y-1 scale-[.98]"
                 class="absolute z-50 w-full mt-2 bg-white rounded-2xl border border-slate-200/80 shadow-[0_20px_60px_-15px_rgba(27,58,107,.25)] overflow-hidden max-h-64 overflow-y-auto">
              <template x-for="(item, i) in resultados" :key="i">
                <button type="button" :data-idx="i" @mousedown.prevent="elegir(item)"
                        class="w-full text-left px-4 py-3 text-sm flex items-center gap-3 transition-colors"
                        :class="i === activo ? 'bg-navy-700 text-white' : 'text-slate-700 hover:bg-navy-50 hover:text-navy-700'">
                  <svg class="w-4 h-4 shrink-0 opacity-50" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75"/>
                  </svg>
                  <span x-text="item"></span>
                </button>
              </template>
              <div x-show="resultados.length === 0 && query.length >= 2"
                   class="px-4 py-3.5 flex items-center gap-3 text-sm text-slate-500">
                <svg class="w-4 h-4 shrink-0 text-gold-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                </svg>
                No está en la lista — se registrará como nuevo colegio.
              </div>
            </div>
          </div>
          <p class="text-xs text-slate-400 mt-1.5">Escribe 2 o más letras y selecciona de la lista.</p>
          @error('colegio_nombre')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>
        @endif

        {{-- Nombre --}}
        <div>
          <label class="block text-sm font-semibold text-slate-600 mb-1.5">Nombre <span class="text-rose-500">*</span></label>
          <input name="nombre" data-field="nombre" value="{{ old('nombre') }}" maxlength="35" placeholder="Erika Maria"
                 @blur="validar('nombre', $el.value)"
                 @input="if(errores.nombre) validar('nombre', $el.value)"
                 :class="errores.nombre ? 'border-rose-400 ring-2 ring-rose-400/20' : ''"
                 class="{{ $inputCls }} @error('nombre') border-rose-400 @enderror">
          <p x-show="errores.nombre" x-cloak x-text="errores.nombre" class="text-xs text-rose-600 mt-1"></p>
          @error('nombre')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Apellido --}}
        <div>
          <label class="block text-sm font-semibold text-slate-600 mb-1.5">Apellido <span class="text-rose-500">*</span></label>
          <input name="apellido" data-field="apellido" value="{{ old('apellido') }}" maxlength="40" placeholder="Pérez Calvimontes"
                 @blur="validar('apellido', $el.value)"
                 @input="if(errores.apellido) validar('apellido', $el.value)"
                 :class="errores.apellido ? 'border-rose-400 ring-2 ring-rose-400/20' : ''"
                 class="{{ $inputCls }} @error('apellido') border-rose-400 @enderror">
          <p x-show="errores.apellido" x-cloak x-text="errores.apellido" class="text-xs text-rose-600 mt-1"></p>
          @error('apellido')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Sexo --}}
        <div class="relative">
          <label class="block text-sm font-semibold text-slate-600 mb-1.5">Sexo <span class="text-rose-500">*</span></label>
          <button type="button" data-field="sexo"
                  @click="sexoAbierto = !sexoAbierto"
                  @click.outside="sexoAbierto = false"
                  class="{{ $inputCls }} flex items-center justify-between @error('sexo') border-rose-400 @enderror"
                  :class="[sexoValor ? 'text-ink' : 'text-slate-400', errores.sexo ? 'border-rose-400 ring-2 ring-rose-400/20' : '']">
            <span x-text="sexoValor || 'Indicar…'"></span>
            <svg class="w-4 h-4 text-slate-400 shrink-0 transition-transform" :class="sexoAbierto ? 'rotate-180' : ''"
                 fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
            </svg>
          </button>
          <input type="hidden" name="sexo" :value="sexoValor">
          <div x-show="sexoAbierto" x-cloak
               x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 -translate-y-1 scale-[.98]" x-transition:enter-end="opacity-100 translate-y-0 scale-100"
               x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 -translate-y-1 scale-[.98]"
               class="absolute z-50 w-full mt-2 bg-white rounded-2xl border border-slate-200/80 shadow-[0_20px_60px_-15px_rgba(27,58,107,.25)] overflow-hidden">
            @foreach (['Femenino','Masculino','Otro'] as $op)
              <button type="button"
                      @click="sexoValor = '{{ $op }}'; sexoAbierto = false; errores.sexo = ''"
                      class="w-full text-left px-4 py-3 text-sm flex items-center gap-3 transition-colors"
                      :class="sexoValor === '{{ $op }}' ? 'bg-navy-700 text-white' : 'text-slate-700 hover:bg-navy-50 hover:text-navy-700'">
                {{ $op }}
              </button>
            @endforeach
          </div>
          <p x-show="errores.sexo" x-cloak x-text="errores.sexo" class="text-xs text-rose-600 mt-1"></p>
          @error('sexo')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Edad --}}
        <div>
          <label class="block text-sm font-semibold text-slate-600 mb-1.5">Edad <span class="text-rose-500">*</span></label>
          <input type="number" name="edad" data-field="edad" value="{{ old('edad') }}" placeholder="18"
                 @blur="validar('edad', $el.value)"
                 @input="if(errores.edad) validar('edad', $el.value)"
                 :class="errores.edad ? 'border-rose-400 ring-2 ring-rose-400/20' : ''"
                 class="{{ $inputCls }} @error('edad') border-rose-400 @enderror">
          <p x-show="errores.edad" x-cloak x-text="errores.edad" class="text-xs text-rose-600 mt-1"></p>
          @error('edad')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Celular --}}
        <div>
          <label class="block text-sm font-semibold text-slate-600 mb-1.5">Celular <span class="text-rose-500">*</span></label>
          <input name="celular" data-field="celular" value="{{ old('celular') }}" maxlength="12" inputmode="numeric" placeholder="Ej: 76543210"
                 @blur="validar('celular', $el.value)"
                 @input="if(errores.celular) validar('celular', $el.value)"
                 :class="errores.celular ? 'border-rose-400 ring-2 ring-rose-400/20' : ''"
                 class="{{ $inputCls }} @error('celular') border-rose-400 @enderror">
          <p x-show="errores.celular" x-cloak x-text="errores.celular" class="text-xs text-rose-600 mt-1"></p>
          @error('celular')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Email --}}
        <div>
          <label class="block text-sm font-semibold text-slate-600 mb-1.5">Correo electrónico</label>
          <input type="email" name="email" value="{{ old('email') }}" maxlength="100" placeholder="nombre@correo.com"
                 class="{{ $inputCls }} @error('email') border-rose-400 @enderror">
          @error('email')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
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
        <div><label class="block text-sm font-semibold text-slate-600 mb-1.5">Nombre de la madre</label><input name="nombre_madre" value="{{ old('nombre_madre') }}" maxlength="60" placeholder="Nombre completo" class="{{ $inputCls }}"></div>
        <div><label class="block text-sm font-semibold text-slate-600 mb-1.5">Celular de la madre</label><input name="celular_madre" value="{{ old('celular_madre') }}" maxlength="12" placeholder="+591 7XXXXXXX" class="{{ $inputCls }}"></div>
        <div><label class="block text-sm font-semibold text-slate-600 mb-1.5">Nombre del padre</label><input name="nombre_padre" value="{{ old('nombre_padre') }}" maxlength="60" placeholder="Nombre completo" class="{{ $inputCls }}"></div>
        <div><label class="block text-sm font-semibold text-slate-600 mb-1.5">Celular del padre</label><input name="celular_padre" value="{{ old('celular_padre') }}" maxlength="12" placeholder="+591 7XXXXXXX" class="{{ $inputCls }}"></div>
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

    Alpine.data('validarFicha', (oldSexo = '') => ({
        sexoAbierto: false,
        sexoValor: oldSexo,
        errores: { nombre: '', apellido: '', sexo: '', edad: '', celular: '' },

        validarCampo(campo, valor) {
            const v = String(valor ?? '').trim();
            switch (campo) {
                case 'nombre': {
                    if (!v) return 'Por favor ingresa tu nombre.';
                    if (v.length < 2) return 'El nombre debe tener al menos 2 caracteres.';
                    if (v.length > 35) return 'El nombre no puede superar los 35 caracteres.';
                    const palabrasNombre = v.split(/\s+/).filter(p => p.length > 0);
                    if (palabrasNombre.length > 4) return 'El nombre no puede tener más de 4 palabras.';
                    if (palabrasNombre.some(p => p.length > 18)) return 'Hay una palabra demasiado larga.';
                    if (palabrasNombre.some(p => /([a-záéíóúüñ])\1{3,}/i.test(p))) return 'No repitas la misma letra muchas veces.';
                    if (!/^[a-záéíóúüñ .'-]+$/i.test(v)) return 'El nombre solo puede contener letras.';
                    return '';
                }
                case 'apellido': {
                    if (!v) return 'Por favor ingresa tu apellido.';
                    if (v.length < 2) return 'El apellido debe tener al menos 2 caracteres.';
                    if (v.length > 40) return 'El apellido no puede superar los 40 caracteres.';
                    const palabrasApellido = v.split(/\s+/).filter(p => p.length > 0);
                    if (palabrasApellido.length > 4) return 'El apellido no puede tener más de 4 palabras.';
                    if (palabrasApellido.some(p => p.length > 18)) return 'Hay una palabra demasiado larga.';
                    if (palabrasApellido.some(p => /([a-záéíóúüñ])\1{3,}/i.test(p))) return 'No repitas la misma letra muchas veces.';
                    if (!/^[a-záéíóúüñ .'-]+$/i.test(v)) return 'El apellido solo puede contener letras.';
                    return '';
                }
                case 'sexo':
                    if (!v) return 'Selecciona una opción.';
                    return '';
                case 'edad':
                    if (!v) return 'Indica tu edad.';
                    const e = parseInt(v, 10);
                    if (isNaN(e) || e < 12 || e > 35) return 'Ingresa una edad válida (entre 12 y 35 años).';
                    return '';
                case 'celular':
                    if (!v) return 'Por favor ingresa tu número de celular.';
                    if (v.length < 7) return 'Ingresa un número de celular válido.';
                    if (v.length > 12) return 'El número de celular es demasiado largo.';
                    return '';
            }
            return '';
        },

        validar(campo, valor) {
            this.errores[campo] = this.validarCampo(campo, valor);
        },

        enviar(form) {
            const valores = {
                nombre:   form.querySelector('[name=nombre]')?.value  ?? '',
                apellido: form.querySelector('[name=apellido]')?.value ?? '',
                sexo:     this.sexoValor,
                edad:     form.querySelector('[name=edad]')?.value    ?? '',
                celular:  form.querySelector('[name=celular]')?.value  ?? '',
            };
            let hayErrores = false;
            for (const [campo, valor] of Object.entries(valores)) {
                this.errores[campo] = this.validarCampo(campo, valor);
                if (this.errores[campo]) hayErrores = true;
            }
            if (!hayErrores) { form.submit(); return; }

            // Scroll y focus al primer campo con error
            this.$nextTick(() => {
                const orden = ['nombre', 'apellido', 'sexo', 'edad', 'celular'];
                for (const campo of orden) {
                    if (!this.errores[campo]) continue;
                    const el = form.querySelector(`[data-field="${campo}"]`);
                    if (el) {
                        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        setTimeout(() => el.focus(), 300);
                    }
                    break;
                }
            });
        },
    }));

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

    }));
});
</script>
@endpush
