@extends('layouts.app')

@section('title', 'Tu resultado')

@php
    $areaP = $areas[$principal];
    $areaS = $areas[$secundaria];
    $labelsLetras = [];
    $valores = [];
    $barColors = [];
    foreach ($ordenAreas as $l) {
        $labelsLetras[] = $l;
        $valores[] = $puntajes[$l];
        $es_top = ($l === $principal || $l === $secundaria);
        $barColors[] = $es_top ? $areas[$l]['color'] : $areas[$l]['color'] . '59';
    }
@endphp

@section('content')
<x-container class="py-10 max-w-4xl">

    {{-- 1. Encabezado --}}
    <header class="text-center">
        <span class="text-[11px] font-semibold uppercase tracking-[0.25em] text-slate-400">Informe de Orientación Vocacional</span>
        <h1 class="font-display font-extrabold text-ink text-4xl sm:text-5xl tracking-tight mt-2">{{ $estudiante->nombre_completo }}</h1>
        <p class="text-sm text-slate-400 mt-2">CHASIDE · {{ $resultado->created_at->format('d/m/Y') }}</p>
    </header>

    {{-- 2. Área dominante --}}
    <section class="mt-8 rounded-3xl bg-white border border-slate-200/70 p-7 sm:p-8 relative overflow-hidden">
        <div class="absolute left-0 inset-y-0 w-1.5" style="background: {{ $areaP['color'] }}"></div>
        <span class="absolute -right-3 -top-10 font-display font-extrabold text-[170px] leading-none select-none pointer-events-none"
              style="color: {{ $areaP['color'] }}; opacity:.06">{{ $principal }}</span>
        <div class="relative flex items-center gap-5">
            <span class="grid place-items-center w-20 h-20 rounded-2xl text-white font-display font-extrabold text-4xl shrink-0"
                  style="background: {{ $areaP['color'] }}">{{ $principal }}</span>
            <div class="min-w-0">
                <span class="text-[11px] uppercase tracking-[0.18em] font-bold" style="color: {{ $areaP['color'] }}">Área vocacional dominante</span>
                <h2 class="font-display font-extrabold text-ink text-3xl sm:text-4xl leading-tight mt-1">{{ $areaP['nombre'] }}</h2>
                <p class="text-slate-500 text-sm mt-1.5">Inclinación secundaria: <b style="color: {{ $areaS['color'] }}">{{ $areaS['nombre'] }}</b></p>
            </div>
        </div>
    </section>

    {{-- 3. Gráfico --}}
    <section class="mt-6 rounded-3xl bg-white border border-slate-200/70 p-6 sm:p-7">
        <h3 class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-400">Puntaje por área</h3>
        <div class="relative h-72 sm:h-80 mt-4"><canvas id="barChaside"></canvas></div>
        <div class="mt-5 grid grid-cols-2 sm:grid-cols-4 gap-x-4 gap-y-2 pt-5 border-t border-slate-100">
            @foreach ($ordenAreas as $l)
                @php $top = ($l === $principal || $l === $secundaria); @endphp
                <div class="flex items-center gap-2 {{ $top ? '' : 'opacity-60' }}">
                    <span class="w-3 h-3 rounded-sm shrink-0" style="background:{{ $areas[$l]['color'] }}"></span>
                    <span class="text-xs text-slate-600"><b class="text-ink">{{ $l }}</b> · {{ $areas[$l]['nombre'] }}</span>
                </div>
            @endforeach
        </div>
    </section>

    {{-- 4. Intereses / Aptitudes --}}
    <section class="mt-6 grid sm:grid-cols-2 gap-6">
        <div class="rounded-3xl bg-white border border-slate-200/70 p-6 sm:p-7">
            <h3 class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-400 mb-4">Tus intereses</h3>
            <div class="flex flex-wrap gap-2">
                @foreach ($areaP['intereses'] as $item)
                    <span class="rounded-full text-sm font-medium px-3.5 py-1.5" style="background:{{ $areaP['color'] }}14; color:{{ $areaP['color'] }}">{{ $item }}</span>
                @endforeach
            </div>
        </div>
        <div class="rounded-3xl bg-white border border-slate-200/70 p-6 sm:p-7">
            <h3 class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-400 mb-4">Tus aptitudes</h3>
            <div class="flex flex-wrap gap-2">
                @foreach ($areaP['aptitudes'] as $item)
                    <span class="rounded-full bg-slate-100 text-slate-600 text-sm font-medium px-3.5 py-1.5">{{ $item }}</span>
                @endforeach
            </div>
        </div>
    </section>

    {{-- 5. Carreras --}}
    <section class="mt-6 rounded-3xl bg-white border border-slate-200/70 p-6 sm:p-7">
        <h3 class="text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-400">Carreras sugeridas</h3>
        <p class="text-sm text-slate-400 mt-1 mb-4">Según tu perfil en {{ $areaP['nombre'] }}</p>
        <div class="grid sm:grid-cols-2 gap-x-8 gap-y-1">
            @foreach ($areaP['carreras'] as $i => $carrera)
                <div class="flex items-center gap-3 py-2.5 border-b border-slate-100">
                    <span class="grid place-items-center w-7 h-7 rounded-lg text-white text-xs font-bold shrink-0" style="background:{{ $areaP['color'] }}">{{ $i+1 }}</span>
                    <span class="font-medium text-ink">{{ $carrera }}</span>
                </div>
            @endforeach
        </div>
    </section>

    {{-- 6. Acciones --}}
    <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
        <a href="{{ route('resultado.pdf', $resultado->id) }}"
           class="group w-full sm:w-auto inline-flex items-center justify-center gap-2.5 rounded-2xl bg-ink hover:bg-navy-700 text-white font-semibold px-8 py-4 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3M4 6a2 2 0 012-2h8l6 6v8a2 2 0 01-2 2H6a2 2 0 01-2-2V6z"/></svg>
            Descargar mi hoja en PDF
        </a>
        <a href="{{ route('reiniciar') }}"
           class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-300 text-slate-600 hover:border-ink hover:text-ink font-semibold px-8 py-4 transition-colors">
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
    const valueLabels = {
      id: 'valueLabels',
      afterDatasetsDraw(chart) {
        const { ctx } = chart;
        const meta = chart.getDatasetMeta(0);
        ctx.save();
        ctx.font = '700 15px "DM Sans"';
        ctx.fillStyle = '#16233f';
        ctx.textAlign = 'center';
        meta.data.forEach((bar, i) => ctx.fillText(chart.data.datasets[0].data[i], bar.x, bar.y - 9));
        ctx.restore();
      },
    };
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: {{ Illuminate\Support\Js::from($labelsLetras) }},
        datasets: [{
          data: {{ Illuminate\Support\Js::from($valores) }},
          backgroundColor: {{ Illuminate\Support\Js::from($barColors) }},
          borderRadius: 10, borderSkipped: false, maxBarThickness: 60,
        }],
      },
      options: {
        responsive: true, maintainAspectRatio: false,
        layout: { padding: { top: 24 } },
        animation: { duration: 1000, easing: 'easeOutQuart' },
        plugins: {
          legend: { display: false },
          tooltip: { backgroundColor: '#16233f', padding: 12, cornerRadius: 10, displayColors: false, callbacks: { label: (it) => `Puntaje: ${it.raw}` } },
        },
        scales: {
          y: { beginAtZero: true, ticks: { precision: 0, stepSize: 2, color: '#94a3b8', font: { size: 12 } }, grid: { color: '#eef2f9' }, border: { display: false } },
          x: { grid: { display: false }, border: { display: false }, ticks: { color: '#16233f', font: { size: 18, weight: '800', family: '"Bricolage Grotesque"' } } },
        },
      },
      plugins: [valueLabels],
    });
  });
</script>
@endpush
