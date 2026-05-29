@extends('layouts.app')

@section('title', 'Tu resultado')

@php
    $areaP = $areas[$principal];
    $areaS = $areas[$secundaria];
    $labelsLetras = [];   // C, H, A, ...
    $valores = [];
    $barColors = [];      // color por barra (top-2 a todo color, resto atenuado)
    foreach ($ordenAreas as $l) {
        $labelsLetras[] = $l;
        $valores[] = $puntajes[$l];
        $es_top = ($l === $principal || $l === $secundaria);
        $barColors[] = $es_top ? $areas[$l]['color'] : $areas[$l]['color'] . '59'; // 35% alpha
    }
@endphp

@section('content')
<x-container class="pt-10 pb-4">

  {{-- Encabezado --}}
  <div class="rise d1 text-center mb-7">
    <span class="inline-flex items-center gap-2 rounded-full bg-gold-500/15 text-gold-600 text-xs font-bold uppercase tracking-[0.18em] px-4 py-2">
      <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 1l2.6 5.3 5.9.9-4.2 4.1 1 5.8L10 14.9 4.7 17.1l1-5.8L1.5 7.2l5.9-.9L10 1z"/></svg>
      Tu informe vocacional
    </span>
    <h1 class="font-display font-extrabold text-ink text-4xl sm:text-5xl tracking-tight mt-3">{{ $estudiante->nombre_completo }}</h1>
    <p class="text-slate-500 mt-1 text-sm">Test concluido el {{ $resultado->created_at->format('d/m/Y') }}</p>
  </div>

  {{-- Area dominante (tarjeta elegante, color como acento) --}}
  <div class="rise d2 card rounded-3xl shadow-card p-7 sm:p-9 mb-6 relative overflow-hidden">
    {{-- Barra de acento lateral con el color del area --}}
    <div class="absolute left-0 inset-y-0 w-1.5" style="background: {{ $areaP['color'] }}"></div>
    {{-- Letra gigante de marca de agua --}}
    <span class="absolute -right-3 -top-10 font-display font-extrabold text-[180px] leading-none select-none pointer-events-none"
          style="color: {{ $areaP['color'] }}; opacity:.07">{{ $principal }}</span>

    <div class="relative flex items-center gap-5">
      <span class="grid place-items-center w-20 h-20 rounded-2xl text-white font-display font-extrabold text-4xl shadow-btn shrink-0"
            style="background: {{ $areaP['color'] }}">{{ $principal }}</span>
      <div class="min-w-0">
        <span class="inline-flex items-center gap-1.5 text-[11px] uppercase tracking-[0.18em] font-bold px-3 py-1 rounded-full"
              style="background: {{ $areaP['color'] }}1a; color: {{ $areaP['color'] }}">
          <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 1l2.6 5.3 5.9.9-4.2 4.1 1 5.8L10 14.9 4.7 17.1l1-5.8L1.5 7.2l5.9-.9z"/></svg>
          Área vocacional dominante
        </span>
        <h2 class="font-display font-extrabold text-ink text-3xl sm:text-4xl mt-2 leading-tight">{{ $areaP['nombre'] }}</h2>
        <p class="text-slate-500 text-sm mt-1.5">Inclinación secundaria:
          <b style="color: {{ $areaS['color'] }}">{{ $areaS['nombre'] }}</b>
        </p>
      </div>
    </div>
  </div>

  {{-- Grafico de barras verticales (puntaje por area) --}}
  <div class="rise d3 card rounded-3xl shadow-card p-6 sm:p-8">
    <div class="flex flex-wrap items-end justify-between gap-2 mb-1">
      <h3 class="font-display font-bold text-ink text-xl">Puntaje por área</h3>
      <span class="text-xs text-slate-400 font-medium">Las 2 áreas más altas son tu vocación</span>
    </div>
    <p class="text-sm text-slate-500 mb-5">Resultado en cada una de las 7 áreas del método CHASIDE.</p>

    <div class="relative h-80 sm:h-96"><canvas id="barChaside"></canvas></div>

    {{-- Leyenda letra → área --}}
    <div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-x-4 gap-y-2 pt-5 border-t border-slate-100">
      @foreach ($ordenAreas as $l)
        @php $top = ($l === $principal || $l === $secundaria); @endphp
        <div class="flex items-center gap-2 {{ $top ? '' : 'opacity-60' }}">
          <span class="w-3 h-3 rounded-sm shrink-0" style="background:{{ $areas[$l]['color'] }}"></span>
          <span class="text-xs text-slate-600"><b class="text-ink">{{ $l }}</b> · {{ $areas[$l]['nombre'] }}</span>
        </div>
      @endforeach
    </div>
  </div>

  {{-- Intereses y aptitudes --}}
  <div class="grid sm:grid-cols-2 gap-6 mt-6">
    <div class="rise d4 card rounded-3xl shadow-card p-6">
      <h3 class="font-display font-bold text-ink text-lg mb-3 flex items-center gap-2">
        <span class="w-7 h-7 grid place-items-center rounded-lg bg-gold-500/15 text-gold-600"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 1l2.6 5.3 5.9.9-4.2 4.1 1 5.8L10 14.9 4.7 17.1l1-5.8L1.5 7.2l5.9-.9z"/></svg></span>
        Tus intereses
      </h3>
      <div class="flex flex-wrap gap-2">
        @foreach ($areaP['intereses'] as $item)
          <span class="rounded-full bg-gold-500/12 text-gold-600 font-semibold text-sm px-3.5 py-1.5">{{ $item }}</span>
        @endforeach
      </div>
    </div>
    <div class="rise d5 card rounded-3xl shadow-card p-6">
      <h3 class="font-display font-bold text-ink text-lg mb-3 flex items-center gap-2">
        <span class="w-7 h-7 grid place-items-center rounded-lg bg-navy-700/10 text-navy-700"><svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>
        Tus aptitudes
      </h3>
      <div class="flex flex-wrap gap-2">
        @foreach ($areaP['aptitudes'] as $item)
          <span class="rounded-full bg-navy-700/8 text-navy-700 font-semibold text-sm px-3.5 py-1.5">{{ $item }}</span>
        @endforeach
      </div>
    </div>
  </div>

  {{-- Carreras --}}
  <div class="rise d5 card rounded-3xl shadow-card p-6 sm:p-7 mt-6">
    <h3 class="font-display font-bold text-ink text-lg">Carreras sugeridas para ti</h3>
    <p class="text-sm text-slate-500 mt-1 mb-4">Según tu perfil en <b class="text-ink">{{ $areaP['nombre'] }}</b>.</p>
    <div class="grid sm:grid-cols-2 gap-3">
      @foreach ($areaP['carreras'] as $i => $carrera)
        <div class="flex items-center gap-3 rounded-2xl bg-white/70 border border-slate-100 px-4 py-3 hover:shadow-card transition">
          <span class="grid place-items-center w-8 h-8 rounded-lg bg-navy-700 text-white font-bold text-sm shrink-0">{{ str_pad($i+1, 2, '0', STR_PAD_LEFT) }}</span>
          <span class="font-semibold text-ink">{{ $carrera }}</span>
        </div>
      @endforeach
    </div>
  </div>

  {{-- Acciones --}}
  <div class="rise d6 flex flex-col sm:flex-row items-center justify-center gap-3 mt-8">
    <a href="{{ route('resultado.pdf', $resultado->id) }}"
       class="group w-full sm:w-auto inline-flex items-center justify-center gap-2.5 rounded-2xl bg-navy-700 hover:bg-navy-600 text-white font-semibold px-8 py-4 shadow-btn hover:-translate-y-0.5 transition-all">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3M4 6a2 2 0 012-2h8l6 6v8a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"/></svg>
      Descargar mi hoja en PDF
    </a>
    <a href="{{ route('reiniciar') }}"
       class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-2xl border-2 border-navy-700 text-navy-700 hover:bg-navy-700 hover:text-white font-semibold px-8 py-4 transition-all">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
      Realizar otro test
    </a>
  </div>
</x-container>
@endsection

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('barChaside');
    if (!ctx) return;

    // Plugin: dibuja el valor encima de cada barra.
    const valueLabels = {
      id: 'valueLabels',
      afterDatasetsDraw(chart) {
        const { ctx } = chart;
        const meta = chart.getDatasetMeta(0);
        ctx.save();
        ctx.font = '700 15px "DM Sans"';
        ctx.fillStyle = '#16233f';
        ctx.textAlign = 'center';
        meta.data.forEach((bar, i) => {
          const v = chart.data.datasets[0].data[i];
          ctx.fillText(v, bar.x, bar.y - 9);
        });
        ctx.restore();
      },
    };

    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: {{ Illuminate\Support\Js::from($labelsLetras) }},
        datasets: [{
          label: 'Puntaje',
          data: {{ Illuminate\Support\Js::from($valores) }},
          backgroundColor: {{ Illuminate\Support\Js::from($barColors) }},
          borderRadius: 10,
          borderSkipped: false,
          maxBarThickness: 64,
        }],
      },
      options: {
        responsive: true, maintainAspectRatio: false,
        layout: { padding: { top: 24 } },
        animation: { duration: 1000, easing: 'easeOutQuart' },
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: '#16233f', padding: 12, cornerRadius: 10, displayColors: false,
            callbacks: { label: (it) => `Puntaje: ${it.raw}` },
          },
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: { precision: 0, stepSize: 2, color: '#94a3b8', font: { size: 12 } },
            grid: { color: '#eef2f9' },
            border: { display: false },
          },
          x: {
            grid: { display: false },
            border: { display: false },
            ticks: { color: '#16233f', font: { size: 18, weight: '800', family: '"Bricolage Grotesque"' } },
          },
        },
      },
      plugins: [valueLabels],
    });
  });
</script>
@endpush
