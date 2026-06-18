@extends('layouts.app')

@section('title', 'Cuestionario')

@push('head')
<style>
  @keyframes qEnter{from{opacity:0;transform:translateY(16px) scale(.985)}to{opacity:1;transform:none}}
  .q-anim{animation:qEnter .34s cubic-bezier(.2,.7,.2,1)}

  @keyframes dotBounce{0%,60%,100%{transform:translateY(0);opacity:1}30%{transform:translateY(-10px);opacity:.5}}
  .dot{width:10px;height:10px;border-radius:50%;background:#c9a14a;display:inline-block;animation:dotBounce 1.4s infinite ease-in-out;}
  .dot:nth-child(1){animation-delay:0s}
  .dot:nth-child(2){animation-delay:.18s}
  .dot:nth-child(3){animation-delay:.36s}
  .dot:nth-child(4){animation-delay:.54s}
  .dot:nth-child(5){animation-delay:.72s}
</style>
@endpush

@php
    $listaPreguntas = [];
    foreach ($preguntas as $n => $texto) { $listaPreguntas[] = ['n' => $n, 'texto' => $texto]; }
@endphp

@section('content')
<div class="mx-auto max-w-2xl px-4 sm:px-6 pt-10 pb-4" x-data="chaside({{ Illuminate\Support\Js::from($listaPreguntas) }}, {{ $estudianteId }})" x-cloak>

  {{-- Barra superior: contador + progreso --}}
  <div class="rise d1 flex items-center justify-between mb-3">
    <span class="inline-flex items-center gap-2 rounded-full bg-white/70 border border-white/80 px-4 py-2 text-sm font-semibold text-navy-700 shadow-card">
      Pregunta <span class="text-gold-600" x-text="current + 1"></span> <span class="text-slate-400">/ <span x-text="total"></span></span>
    </span>
    <span class="text-sm font-semibold text-slate-500"><span class="text-navy-700" x-text="contestadas"></span> respondidas</span>
  </div>
  <div class="rise d1 h-3 rounded-full bg-white/70 shadow-inner overflow-hidden">
    <div class="h-full rounded-full bg-gold-500 transition-all duration-500 ease-out"
         :style="`width:${(contestadas/total)*100}%`"></div>
  </div>

  {{-- Tarjeta de la pregunta --}}
  <div class="rise d2 card rounded-4xl shadow-float mt-6 p-7 sm:p-10 relative overflow-hidden">
    <div class="absolute -top-10 -right-10 w-32 h-32 rounded-full bg-navy-500/10 blur-2xl"></div>
    <div class="relative" x-ref="qbox">
      <span class="inline-block font-display font-extrabold text-6xl sm:text-7xl text-navy-700/25 leading-none"
            x-text="String(preguntas[current].n).padStart(2,'0')"></span>
      <p class="font-display font-semibold text-ink text-2xl sm:text-3xl leading-snug mt-4 min-h-[110px]"
         x-text="preguntas[current].texto"></p>

      {{-- Botones SI / NO --}}
      <div class="grid grid-cols-2 gap-4 mt-7">
        <button type="button" @click="responder(true)"
          class="group flex items-center justify-center gap-2.5 min-h-[68px] rounded-2xl border-2 font-bold text-lg transition-all"
          :class="answers[preguntas[current].n] === true
            ? 'bg-navy-700 border-transparent text-white shadow-btn -translate-y-0.5'
            : 'bg-white border-slate-200 text-slate-700 hover:border-navy-500 hover:-translate-y-0.5'">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
          SÍ
        </button>
        <button type="button" @click="responder(false)"
          class="group flex items-center justify-center gap-2.5 min-h-[68px] rounded-2xl border-2 font-bold text-lg transition-all"
          :class="answers[preguntas[current].n] === false
            ? 'bg-rose-600 border-transparent text-white shadow-btn -translate-y-0.5'
            : 'bg-white border-slate-200 text-slate-700 hover:border-rose-400 hover:-translate-y-0.5'">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
          NO
        </button>
      </div>
    </div>
  </div>

  {{-- Navegacion --}}
  <div class="rise d3 flex items-center justify-between mt-5">
    <button type="button" @click="anterior()" :disabled="current === 0"
      class="inline-flex items-center gap-1.5 rounded-xl px-5 py-3 font-semibold text-slate-600 hover:bg-white/70 disabled:opacity-30 disabled:cursor-not-allowed transition">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
      Anterior
    </button>
    <button type="button" @click="siguiente()" x-show="current < total - 1"
      class="inline-flex items-center gap-1.5 rounded-xl px-5 py-3 font-semibold text-navy-700 hover:bg-white/70 transition">
      Siguiente
      <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
    </button>
  </div>

  {{-- Envio / pendientes --}}
  <div class="rise d4 mt-7 card rounded-3xl shadow-card p-5 text-center">
    <template x-if="contestadas < total">
      <p class="text-sm text-slate-500">
        Te faltan <span class="font-bold text-navy-700" x-text="total - contestadas"></span> preguntas.
        <button type="button" @click="irAPendiente()" class="text-gold-600 font-semibold underline underline-offset-2 ml-1">Completar las que faltan</button>
      </p>
    </template>
    <form method="POST" action="{{ route('resultado.calcular') }}" x-show="contestadas === total" @submit="limpiarGuardado()">
      @csrf
      <template x-for="p in preguntas" :key="p.n">
        <input type="hidden" :name="`respuestas[${p.n}]`" :value="answers[p.n] ? 1 : 0">
      </template>
      <button type="submit" x-ref="btnEnviar" class="hidden"></button>
      <button type="button" @click="limpiarGuardado(); $refs.btnEnviar.click()"
        class="group inline-flex items-center justify-center gap-2.5 rounded-2xl bg-gold-500 hover:bg-gold-600 text-ink font-bold text-base px-8 py-3.5 shadow-gold hover:-translate-y-0.5 transition-all">
        Salir
        <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
        </svg>
      </button>
    </form>
  </div>

  <p class="text-center text-xs text-slate-400 mt-4">Responde con sinceridad. No hay respuestas correctas ni incorrectas.</p>

  {{-- BOTÓN DE PRUEBA: llena las 98 respuestas al azar y envía --}}
  <div class="mt-6 flex justify-center">
    <button type="button" @click="llenarAlAzar()"
      class="inline-flex items-center gap-2 rounded-xl border border-dashed border-amber-400 bg-amber-50 text-amber-700 text-xs font-semibold px-4 py-2.5 hover:bg-amber-100 transition">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 00-3.7-3.7 48.678 48.678 0 00-7.324 0 4.006 4.006 0 00-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3l-3-3m-12 3c0 1.232.046 2.453.138 3.662a4.006 4.006 0 003.7 3.7 48.656 48.656 0 007.324 0 4.006 4.006 0 003.7-3.7c.017-.22.032-.441.046-.662M4.5 12l3 3m-3-3l-3 3"/>
      </svg>
      [PRUEBA] Llenar al azar y enviar
    </button>
  </div>

  <x-modal-test-completado />

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('chaside', (listaPreguntas, estudianteId) => ({
            preguntas: listaPreguntas,
            total: listaPreguntas.length,
            current: 0,
            answers: {},
            modalFin: false,
            storageKey: 'chaside_test_' + estudianteId,
            init() {
                this.restaurar();
                this.$watch('current', () => { this.flashPregunta(); this.persistir(); });
            },
            restaurar() {
                try {
                    Object.keys(localStorage).forEach(k => {
                        if (k.startsWith('chaside_test_') && k !== this.storageKey) localStorage.removeItem(k);
                    });
                    const data = JSON.parse(localStorage.getItem(this.storageKey) || 'null');
                    if (!data) return;
                    this.answers = data.answers || {};
                    if (Number.isInteger(data.current) && data.current >= 0 && data.current < this.total) {
                        this.current = data.current;
                    }
                } catch (e) {}
            },
            persistir() {
                try {
                    localStorage.setItem(this.storageKey, JSON.stringify({ current: this.current, answers: this.answers }));
                } catch (e) {}
            },
            limpiarGuardado() {
                try { localStorage.removeItem(this.storageKey); } catch (e) {}
            },
            flashPregunta() {
                const el = this.$refs.qbox;
                if (!el) return;
                el.classList.remove('q-anim');
                void el.offsetWidth;
                el.classList.add('q-anim');
            },
            get contestadas() { return Object.keys(this.answers).length; },
            proximaPendiente() {
                for (let i = 1; i <= this.total; i++) {
                    const idx = (this.current + i) % this.total;
                    if (!(this.preguntas[idx].n in this.answers)) return idx;
                }
                return -1;
            },
            responder(valor) {
                this.answers[this.preguntas[this.current].n] = valor;
                this.persistir();
                const siguiente = this.proximaPendiente();
                if (siguiente !== -1) {
                    setTimeout(() => { this.current = siguiente; }, 400);
                } else {
                    // Todas respondidas: muestra el modal tras la animacion del ultimo click
                    setTimeout(() => { this.modalFin = true; }, 550);
                }
            },
            anterior() { if (this.current > 0) this.current--; },
            siguiente() { if (this.current < this.total - 1) this.current++; },
            irAPendiente() {
                const idx = this.proximaPendiente();
                if (idx !== -1) this.current = idx;
            },
            llenarAlAzar() {
                this.preguntas.forEach(p => { this.answers[p.n] = Math.random() < 0.5; });
                this.persistir();
                this.modalFin = true;
            },
        }));
    });
</script>
@endpush
