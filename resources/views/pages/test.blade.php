@extends('layouts.app')

@section('title', 'Cuestionario')

@php
    $listaPreguntas = [];
    foreach ($preguntas as $n => $texto) { $listaPreguntas[] = ['n' => $n, 'texto' => $texto]; }
@endphp

@section('content')
<div class="mx-auto max-w-2xl px-4 sm:px-6 pt-10 pb-4" x-data="chaside({{ Illuminate\Support\Js::from($listaPreguntas) }})" x-cloak>

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
    <div class="relative" x-transition>
      <span class="inline-block font-display font-extrabold text-6xl sm:text-7xl text-navy-700/15 leading-none"
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
        <button type="button" @click="irAPendiente()" class="text-gold-600 font-semibold underline underline-offset-2 ml-1">Ir a la pendiente</button>
      </p>
    </template>
    <form method="POST" action="{{ route('resultado.calcular') }}" x-show="contestadas === total">
      @csrf
      <template x-for="p in preguntas" :key="p.n">
        <input type="hidden" :name="`respuestas[${p.n}]`" :value="answers[p.n] ? 1 : 0">
      </template>
      <button type="submit"
        class="group w-full inline-flex items-center justify-center gap-2.5 rounded-2xl bg-gold-500 hover:bg-gold-600 text-ink font-bold text-lg px-8 py-4 shadow-gold hover:-translate-y-0.5 transition-all">
        Ver mi resultado
        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
      </button>
    </form>
  </div>

  <p class="text-center text-xs text-slate-400 mt-4">Responde con sinceridad. No hay respuestas correctas ni incorrectas.</p>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('chaside', (listaPreguntas) => ({
            preguntas: listaPreguntas,
            total: listaPreguntas.length,
            current: 0,
            answers: {},
            get contestadas() { return Object.keys(this.answers).length; },
            responder(valor) {
                this.answers[this.preguntas[this.current].n] = valor;
                if (this.current < this.total - 1) {
                    setTimeout(() => { if (this.current < this.total - 1) this.current++; }, 400);
                }
            },
            anterior() { if (this.current > 0) this.current--; },
            siguiente() { if (this.current < this.total - 1) this.current++; },
            irAPendiente() {
                const idx = this.preguntas.findIndex(p => !(p.n in this.answers));
                if (idx !== -1) this.current = idx;
            },
        }));
    });
</script>
@endpush
