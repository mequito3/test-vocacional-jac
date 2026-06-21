@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
<x-container>

  {{-- HERO --}}
  <section class="pt-10 sm:pt-16 pb-10 grid lg:grid-cols-[1.1fr_.9fr] gap-10 items-center">
    <div>
      <span class="rise d1 inline-flex items-center gap-2 rounded-full bg-white/70 backdrop-blur border border-white/80 text-navy-600 text-xs font-semibold px-4 py-2 shadow-card">
        <span class="w-2 h-2 rounded-full bg-gold-500 animate-pulse"></span>
        Test de Orientación Vocacional · Gratuito
      </span>
      <h1 class="rise d2 font-display font-extrabold text-ink leading-[1.02] tracking-tight text-5xl sm:text-6xl lg:text-7xl mt-6">
        Descubre tu<br><span class="text-gold-500">vocación</span> ideal.
      </h1>
      <p class="rise d3 text-slate-600 text-lg mt-5 max-w-xl leading-relaxed">
        El método <strong class="text-ink">CHASIDE</strong> cruza tus <strong class="text-ink">intereses</strong> con tus
        <strong class="text-ink">aptitudes</strong> y te muestra las áreas y carreras donde más vas a brillar.
      </p>
      <div class="rise d4 flex flex-wrap items-center gap-4 mt-8">
        <a href="{{ route('registro') }}"
           class="group inline-flex items-center gap-2.5 rounded-2xl bg-navy-700 hover:bg-navy-600 text-white font-semibold text-lg px-8 py-4 shadow-btn hover:-translate-y-0.5 transition-all">
          Comenzar ahora
          <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
        </a>
        <div class="flex items-center gap-2 text-slate-500 text-sm font-medium">
          <svg class="w-5 h-5 text-gold-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10 1l2.6 5.3 5.9.9-4.2 4.1 1 5.8L10 14.9 4.7 17.1l1-5.8L1.5 7.2l5.9-.9L10 1z"/></svg>
          Resultado al instante
        </div>
      </div>
    </div>

    {{-- Tarjeta visual: las 7 áreas --}}
    <div class="rise d4 relative">
      <div class="card rounded-4xl shadow-float p-7 sm:p-8 relative overflow-hidden">
        <div class="absolute -top-8 -right-8 w-32 h-32 rounded-full bg-gold-500/20 blur-2xl"></div>
        <p class="text-xs uppercase tracking-[0.2em] text-navy-600 font-bold">Explora 7 áreas</p>
        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-2.5">
          @foreach (\App\Support\Chaside::ORDEN_AREAS as $letra)
            @php $a = \App\Support\Chaside::AREAS[$letra]; @endphp
            <div class="flex items-center gap-2.5 rounded-2xl bg-white/70 px-3 py-2.5 shadow-card">
              <span class="grid place-items-center w-8 h-8 rounded-lg text-white font-display font-bold text-sm shrink-0" style="background:{{ $a['color'] }}">{{ $letra }}</span>
              <span class="text-sm font-semibold text-ink whitespace-normal break-words leading-tight">{{ $a['nombre'] }}</span>
            </div>
          @endforeach
          <div class="flex items-center justify-center rounded-2xl bg-navy-700 text-white px-3 py-2.5 shadow-btn">
            <span class="font-display font-bold text-2xl">98</span>
            <span class="text-xs ml-2 leading-tight">preguntas<br>Sí / No</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- TARJETAS DE BENEFICIO --}}
  <section class="grid sm:grid-cols-3 gap-5 py-6">
    @php
      $feats = [
        ['#2557a7','M13 10V3L4 14h7v7l9-11h-7z','Rápido y simple','98 preguntas de Sí o No. Te toma unos 15 minutos.'],
        ['#059669','M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z','Resultado confiable','Un gráfico claro con tu área dominante y la secundaria.'],
        ['#dc8f12','M12 6.25a5.75 5.75 0 100 11.5 5.75 5.75 0 000-11.5zM12 2v2m0 16v2m10-10h-2M4 12H2','Carreras a tu medida','Sugerencias de carreras según tus intereses y aptitudes.'],
      ];
    @endphp
    @foreach ($feats as $i => [$c,$path,$t,$d])
      <div class="rise d{{ $i+2 }} card rounded-3xl shadow-card p-6 hover:shadow-float hover:-translate-y-1 transition-all">
        <span class="grid place-items-center w-12 h-12 rounded-2xl mb-4" style="background:{{ $c }}1a;color:{{ $c }}">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $path }}"/></svg>
        </span>
        <h3 class="font-display font-bold text-ink text-lg">{{ $t }}</h3>
        <p class="text-slate-500 text-sm mt-1.5 leading-relaxed">{{ $d }}</p>
      </div>
    @endforeach
  </section>

  {{-- PASOS --}}
  <section class="my-8 card rounded-4xl shadow-card overflow-hidden">
    <div class="grid sm:grid-cols-3">
      @php $pasos = [['01','Completa tu ficha','Tus datos personales básicos.'],['02','Responde el test','98 preguntas, una a una.'],['03','Recibe tu informe','Tu vocación y carreras afines.']]; @endphp
      @foreach ($pasos as $i => [$n,$t,$d])
        <div class="p-7 {{ $i < 2 ? 'sm:border-r' : '' }} border-b sm:border-b-0 border-slate-200/70 flex gap-4">
          <span class="font-display font-extrabold text-3xl text-gold-500/90">{{ $n }}</span>
          <div>
            <p class="font-display font-bold text-ink">{{ $t }}</p>
            <p class="text-sm text-slate-500 mt-0.5">{{ $d }}</p>
          </div>
        </div>
      @endforeach
    </div>
  </section>
</x-container>
@endsection
