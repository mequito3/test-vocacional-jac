@extends('layouts.admin')

@section('title', $colegio ? $colegio->nombre : 'Sin colegio')

@section('content')

<div x-data="adminColegio()" @keydown.escape.window="cerrar()">

  {{-- Encabezado --}}
  <div class="mb-7">
    <a href="{{ route('admin.dashboard') }}"
       class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-navy-700 font-semibold transition-colors mb-4">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
      </svg>
      Panel
    </a>

    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
      <div>
        <h1 class="font-display font-extrabold text-ink text-3xl">
          {{ $colegio ? $colegio->nombre : 'Sin colegio asignado' }}
        </h1>
        <p class="text-slate-500 text-sm mt-1">
          {{ $estudiantes->count() }} {{ $estudiantes->count() === 1 ? 'estudiante registrado' : 'estudiantes registrados' }}
        </p>
      </div>

      @if ($colegio)
      @php $enlace = url('/grupo/' . $colegio->token); @endphp
      <div class="shrink-0 w-full sm:w-auto" x-data="{copiado: false}">
        <p class="text-xs font-semibold text-slate-400 uppercase tracking-widest mb-1.5">Enlace de registro</p>
        <div class="flex items-center gap-2 rounded-2xl bg-white border border-slate-200 shadow-sm px-4 py-2.5">
          <span class="text-sm font-mono text-slate-600 truncate max-w-xs select-all">{{ $enlace }}</span>
          <button @click="navigator.clipboard.writeText('{{ $enlace }}').then(() => { copiado = true; setTimeout(() => copiado = false, 2500) })"
                  class="shrink-0 text-xs font-semibold px-3 py-1.5 rounded-xl transition-all"
                  :class="copiado ? 'bg-emerald-100 text-emerald-700' : 'bg-navy-50 text-navy-700 hover:bg-navy-100'">
            <span x-text="copiado ? '✓ Copiado' : 'Copiar'"></span>
          </button>
        </div>
        <p class="text-xs text-slate-400 mt-1.5">Comparte este enlace con el colegio para que los alumnos hagan el test.</p>
      </div>
      @endif
    </div>
  </div>

  {{-- Tabla de estudiantes --}}
  @if ($estudiantes->isEmpty())
    <div class="rounded-3xl border-2 border-dashed border-slate-200 p-12 text-center">
      <div class="w-14 h-14 rounded-2xl bg-slate-100 grid place-items-center mx-auto mb-4">
        <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
        </svg>
      </div>
      <p class="font-semibold text-slate-600">Aún no hay estudiantes en este grupo.</p>
    </div>

  @else
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-visible">

      <div class="hidden sm:grid grid-cols-[2fr_1.4fr_1fr_1.6fr] gap-4 px-6 py-3.5 bg-slate-50 border-b border-slate-200 text-xs font-bold uppercase tracking-widest text-slate-400">
        <span>Estudiante</span>
        <span>Área principal</span>
        <span>Fecha</span>
        <span class="text-right">Acciones</span>
      </div>

      @foreach ($estudiantes as $i => $e)
        @php
          $resultado = $e->resultados->first();
          $area      = $resultado ? \App\Support\Chaside::AREAS[$resultado->area_principal] : null;

          // Datos para el modal Alpine (solo si tiene resultado completo)
          $modalData = null;
          if ($resultado && $resultado->share_token) {
              $modalData = [
                  'nombre'     => $e->nombre_completo,
                  'sexo'       => $e->sexo,
                  'edad'       => $e->edad,
                  'celular'    => $e->celular ?? '',
                  'fecha'      => $resultado->created_at->format('d/m/Y'),
                  'area_p'     => $resultado->area_principal,
                  'area_s'     => $resultado->area_secundaria,
                  'puntajes'   => $resultado->puntajes(),
                  'share_token'=> $resultado->share_token,
                  'share_url'  => route('resultado.share', $resultado->share_token),
                  'pdf_url'    => route('resultado.share.pdf', $resultado->share_token),
              ];
          }
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-[2fr_1.4fr_1fr_1.6fr] gap-3 sm:gap-4 px-6 py-4
                    {{ $i > 0 ? 'border-t border-slate-100' : '' }} hover:bg-slate-50/60 transition-colors">

          {{-- Nombre --}}
          <div class="flex items-center gap-3">
            <div class="grid place-items-center w-9 h-9 rounded-xl bg-navy-50 text-navy-700 font-display font-extrabold text-sm shrink-0">
              {{ mb_substr($e->nombre, 0, 1) }}
            </div>
            <div class="min-w-0">
              <p class="font-semibold text-ink text-sm truncate">{{ $e->nombre_completo }}</p>
              <p class="text-xs text-slate-400">{{ $e->sexo }}, {{ $e->edad }} años</p>
            </div>
          </div>

          {{-- Área --}}
          <div class="flex items-center">
            @if ($resultado && $area)
              <div class="flex items-center gap-2">
                <span class="grid place-items-center w-7 h-7 rounded-lg text-white font-display font-bold text-xs shrink-0"
                      style="background: {{ $area['color'] }}">{{ $resultado->area_principal }}</span>
                <span class="text-sm text-slate-700 font-medium leading-tight">{{ $area['nombre'] }}</span>
              </div>
            @else
              <span class="inline-block rounded-full bg-amber-50 text-amber-700 text-xs font-semibold px-2.5 py-1 border border-amber-200">
                Pendiente
              </span>
            @endif
          </div>

          {{-- Fecha --}}
          <div class="flex items-center text-sm text-slate-500">
            @if ($resultado)
              {{ $resultado->created_at->format('d/m/Y') }}
            @else
              <span class="text-slate-300">—</span>
            @endif
          </div>

          {{-- Acciones --}}
          <div class="flex items-center justify-start sm:justify-end">
            @if ($modalData)

              <div class="relative" x-data="{menu: false}">
                <button @click="menu = !menu"
                        @click.outside="menu = false"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-semibold text-xs px-3 py-2 transition-all shadow-sm">
                  Acciones
                  <svg class="w-3 h-3 text-slate-400 transition-transform" :class="menu ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                  </svg>
                </button>

                <div x-show="menu" x-cloak x-transition
                     class="absolute right-0 mt-1.5 w-56 bg-white rounded-2xl shadow-xl border border-slate-200 py-1.5 z-20">

                  {{-- Ver resultado --}}
                  <button type="button"
                          @click="ver({{ Illuminate\Support\Js::from($modalData) }}); menu = false"
                          class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-ink hover:bg-slate-50 transition-colors">
                    <svg class="w-4 h-4 text-navy-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Ver resultado
                  </button>

                  {{-- Descargar PDF --}}
                  <a href="{{ route('resultado.share.pdf', $resultado->share_token) }}"
                     @click="menu = false"
                     class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-ink hover:bg-slate-50 transition-colors">
                    <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                    </svg>
                    Descargar PDF
                  </a>

                  <div class="my-1 border-t border-slate-100"></div>

                  {{-- Compartir PDF por WhatsApp --}}
                  <button type="button"
                          @click="enviarWA({{ \Illuminate\Support\Js::from($modalData['pdf_url']) }}, {{ \Illuminate\Support\Js::from($e->nombre_completo) }}); menu = false"
                          :disabled="enviando"
                          class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-ink hover:bg-emerald-50 transition-colors disabled:opacity-50">
                    <svg class="w-4 h-4 text-[#25D366] shrink-0" viewBox="0 0 24 24" fill="currentColor">
                      <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                    </svg>
                    <span x-text="enviando ? 'Preparando…' : 'Compartir PDF'"></span>
                  </button>

                  {{-- Mensaje directo al número --}}
                  @if($e->celular)
                  <button type="button"
                          @click="enviarWAMensaje(
                              {{ \Illuminate\Support\Js::from($e->celular) }},
                              {{ \Illuminate\Support\Js::from($e->nombre_completo) }},
                              {{ \Illuminate\Support\Js::from($modalData['share_url']) }},
                              {{ \Illuminate\Support\Js::from($area['nombre']) }}
                          ); menu = false"
                          class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-[#128C7E] hover:bg-emerald-50 transition-colors">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z"/>
                    </svg>
                    Enviar a {{ $e->celular }}
                  </button>
                  @endif

                </div>
              </div>

            @elseif ($resultado)
              <span class="text-xs text-slate-400 italic">Sin enlace</span>
            @else
              <span class="text-xs text-slate-300">—</span>
            @endif
          </div>
        </div>
      @endforeach
    </div>
  @endif

  {{-- ====== MODAL ====== --}}
  {{-- Overlay --}}
  <div x-show="modal.abierto"
       x-transition:enter="transition ease-out duration-200"
       x-transition:enter-start="opacity-0"
       x-transition:enter-end="opacity-100"
       x-transition:leave="transition ease-in duration-150"
       x-transition:leave-start="opacity-100"
       x-transition:leave-end="opacity-0"
       x-cloak
       @click="cerrar()"
       class="fixed inset-0 bg-ink/50 backdrop-blur-sm z-40"></div>

  {{-- Card --}}
  <div x-show="modal.abierto"
       x-transition:enter="transition ease-out duration-200"
       x-transition:enter-start="opacity-0 scale-95 translate-y-3"
       x-transition:enter-end="opacity-100 scale-100 translate-y-0"
       x-transition:leave="transition ease-in duration-150"
       x-transition:leave-start="opacity-100 scale-100 translate-y-0"
       x-transition:leave-end="opacity-0 scale-95 translate-y-3"
       x-cloak
       class="fixed inset-0 z-50 flex items-center justify-center p-4 pointer-events-none">

    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-xl max-h-[88vh] overflow-y-auto pointer-events-auto"
         @click.stop>

      {{-- Header del modal --}}
      <div class="sticky top-0 bg-white/95 backdrop-blur border-b border-slate-100 px-6 py-4 flex items-start justify-between gap-3 rounded-t-3xl z-10">
        <div class="min-w-0">
          <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400 mb-0.5">Resultado CHASIDE</p>
          <h2 class="font-display font-extrabold text-ink text-xl leading-snug truncate" x-text="modal.data.nombre"></h2>
          <p class="text-xs text-slate-400 mt-0.5"
             x-text="modal.data.celular
               ? `${modal.data.sexo} · ${modal.data.edad} años · ${modal.data.celular} · ${modal.data.fecha}`
               : `${modal.data.sexo} · ${modal.data.edad} años · ${modal.data.fecha}`"></p>
        </div>
        <button @click="cerrar()"
                class="shrink-0 grid place-items-center w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 transition-colors mt-0.5">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <div class="px-6 py-5 space-y-5">

        {{-- Área principal --}}
        <div class="rounded-2xl border border-slate-200 p-5 relative overflow-hidden">
          <div class="absolute left-0 inset-y-0 w-1.5 rounded-l-2xl" :style="`background:${modal.data.areaP.color}`"></div>
          <div class="flex items-center gap-4">
            <span class="grid place-items-center w-14 h-14 rounded-xl text-white font-display font-extrabold text-2xl shrink-0"
                  :style="`background:${modal.data.areaP.color}`"
                  x-text="modal.data.area_p"></span>
            <div>
              <p class="text-[10px] uppercase tracking-[0.18em] font-bold" :style="`color:${modal.data.areaP.color}`">Área dominante</p>
              <h3 class="font-display font-extrabold text-ink text-xl leading-tight" x-text="modal.data.areaP.nombre"></h3>
              <p class="text-xs text-slate-500 mt-0.5">
                Secundaria:
                <b :style="`color:${modal.data.areaS.color}`" x-text="modal.data.areaS.nombre"></b>
              </p>
            </div>
            <div class="ml-auto pl-4 border-l border-slate-200 text-center shrink-0 hidden sm:block">
              <span class="font-display font-extrabold text-3xl" :style="`color:${modal.data.areaP.color}`"
                    x-text="modal.data.puntajes[modal.data.area_p]"></span>
              <p class="text-[10px] uppercase tracking-wide text-slate-400 font-semibold">pts</p>
            </div>
          </div>
        </div>

        {{-- Barras de puntaje --}}
        <div>
          <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 mb-3">Puntaje por área</p>
          <div class="space-y-2">
            <template x-for="letra in orden" :key="letra">
              <div class="flex items-center gap-3">
                <span class="w-5 text-center font-display font-bold text-sm shrink-0"
                      :style="`color:${areas[letra].color}`"
                      x-text="letra"></span>
                <div class="flex-1 h-3 bg-slate-100 rounded-full overflow-hidden">
                  <div class="h-full rounded-full transition-all duration-500"
                       :style="`width:${(modal.data.puntajes[letra]/14)*100}%;
                                background:${
                                  letra === modal.data.area_p ? areas[letra].color :
                                  letra === modal.data.area_s ? areas[letra].color+'bb' :
                                                                areas[letra].color+'44'
                                }`"></div>
                </div>
                <span class="w-5 text-right text-sm shrink-0"
                      :class="letra === modal.data.area_p ? 'font-bold text-ink' : 'text-slate-400'"
                      x-text="modal.data.puntajes[letra]"></span>
              </div>
            </template>
          </div>
        </div>

        {{-- Intereses --}}
        <div>
          <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 mb-2.5">Intereses</p>
          <div class="flex flex-wrap gap-1.5">
            <template x-for="item in modal.data.areaP.intereses" :key="item">
              <span class="rounded-full text-xs font-semibold px-3 py-1.5"
                    :style="`background:${modal.data.areaP.color}18;color:${modal.data.areaP.color}`"
                    x-text="item"></span>
            </template>
          </div>
        </div>

        {{-- Aptitudes --}}
        <div>
          <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 mb-2.5">Aptitudes</p>
          <div class="flex flex-wrap gap-1.5">
            <template x-for="item in modal.data.areaP.aptitudes" :key="item">
              <span class="rounded-full bg-slate-100 text-slate-600 text-xs font-semibold px-3 py-1.5"
                    x-text="item"></span>
            </template>
          </div>
        </div>

        {{-- Carreras --}}
        <div>
          <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 mb-2.5">Carreras sugeridas</p>
          <div class="grid grid-cols-2 gap-x-4 gap-y-1.5">
            <template x-for="(c, idx) in modal.data.areaP.carreras" :key="idx">
              <div class="flex items-center gap-2">
                <span class="grid place-items-center w-5 h-5 rounded text-white text-[10px] font-bold shrink-0"
                      :style="`background:${modal.data.areaP.color}`"
                      x-text="idx + 1"></span>
                <span class="text-sm text-ink font-medium truncate" x-text="c"></span>
              </div>
            </template>
          </div>
        </div>
      </div>

      {{-- Footer del modal --}}
      <div class="sticky bottom-0 bg-white/95 backdrop-blur border-t border-slate-100 px-6 py-4 flex flex-col gap-2 rounded-b-3xl">

        {{-- Compartir PDF --}}
        <button type="button"
                @click="enviarWA(modal.data.pdf_url, modal.data.nombre)"
                :disabled="enviando"
                title="Descargar y compartir PDF como archivo"
                class="w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-ink hover:bg-navy-700 text-white font-semibold text-sm px-4 py-3 transition-colors disabled:opacity-60">
          <svg x-show="!enviando" class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z"/>
          </svg>
          <svg x-show="enviando" class="w-4 h-4 shrink-0 animate-spin" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.990"/>
          </svg>
          <span x-text="enviando ? 'Preparando…' : 'Compartir PDF'"></span>
        </button>

        {{-- Fila 2: WhatsApp con mensaje directo al número del estudiante --}}
        <button type="button"
                @click="enviarWAMensaje(modal.data.celular, modal.data.nombre, modal.data.share_url, modal.data.areaP.nombre)"
                :disabled="!modal.data.celular"
                :title="modal.data.celular ? 'Abrir WhatsApp con mensaje listo para ' + modal.data.celular : 'Este estudiante no registró su número'"
                :class="modal.data.celular
                  ? 'bg-[#25D366] hover:bg-[#128C7E] text-white cursor-pointer'
                  : 'bg-slate-100 text-slate-400 cursor-not-allowed'"
                class="w-full inline-flex items-center justify-center gap-2 rounded-2xl font-semibold text-sm px-4 py-3 transition-colors">
          <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="currentColor">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
          </svg>
          <span x-text="modal.data.celular
            ? 'Enviar mensaje a ' + modal.data.celular
            : 'Sin número registrado'"></span>
        </button>

      </div>

    </div>
  </div>
  {{-- ====== FIN MODAL ====== --}}

  {{-- Banner tiempo real --}}
  @if($colegio)
  <div id="nuevo-resultado-banner"
       class="hidden fixed bottom-5 right-5 z-50 flex items-center gap-3 bg-emerald-700 text-white rounded-2xl shadow-xl px-5 py-3.5">
    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <span class="font-semibold text-sm">Nuevo resultado disponible</span>
    <button onclick="location.reload()" class="ml-1 bg-white/20 hover:bg-white/30 rounded-xl px-3 py-1.5 text-xs font-bold transition-colors">
      Actualizar
    </button>
  </div>
  @endif

</div>

@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('adminColegio', () => ({
        areas: {!! \Illuminate\Support\Js::from(\App\Support\Chaside::AREAS) !!},
        orden: {!! \Illuminate\Support\Js::from(\App\Support\Chaside::ORDEN_AREAS) !!},
        enviando: false,

        modal: {
            abierto: false,
            data: {
                nombre: '', sexo: '', edad: 0, fecha: '', celular: '',
                area_p: 'C', area_s: 'H',
                puntajes: { C:0, H:0, A:0, S:0, I:0, D:0, E:0 },
                share_token: '', share_url: '#', pdf_url: '#',
                areaP: { nombre:'', color:'#1b3a6b', intereses:[], aptitudes:[], carreras:[] },
                areaS: { nombre:'', color:'#1b3a6b' },
            },
        },

        ver(data) {
            this.modal.data = {
                ...data,
                areaP: this.areas[data.area_p],
                areaS: this.areas[data.area_s],
            };
            this.modal.abierto = true;
            document.body.style.overflow = 'hidden';
        },

        cerrar() {
            this.modal.abierto = false;
            document.body.style.overflow = '';
        },

        enviarWAMensaje(celular, nombre, shareUrl, areaNombre) {
            const limpio = celular.replace(/\D/g, '');
            const numero = limpio.length <= 8 ? '591' + limpio : limpio;
            const texto = [
                `Hola ${nombre} 👋`,
                ``,
                `Tu resultado del *Test Vocacional CHASIDE* (JAC Bolivia 2000) está listo.`,
                ``,
                `🎯 Área dominante: *${areaNombre}*`,
                ``,
                `👉 Ver tu resultado aquí:`,
                shareUrl,
                ``,
                `¡Mucho éxito en tu camino vocacional! 🎓`,
            ].join('\n');
            window.open(`https://wa.me/${numero}?text=${encodeURIComponent(texto)}`, '_blank');
        },

        async enviarWA(pdfUrl, nombre) {
            if (this.enviando) return;
            this.enviando = true;
            try {
                const resp = await fetch(pdfUrl);
                if (!resp.ok) throw new Error('error');
                const blob = await resp.blob();
                const slug = nombre.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '').replace(/\s+/g, '-');
                const file = new File([blob], 'informe-chaside-' + slug + '.pdf', { type: 'application/pdf' });
                if (navigator.canShare && navigator.canShare({ files: [file] })) {
                    await navigator.share({ files: [file] });
                } else {
                    // Fallback: descarga el PDF localmente
                    const url = URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url; a.download = 'informe-chaside-' + slug + '.pdf'; a.click();
                    setTimeout(() => URL.revokeObjectURL(url), 3000);
                }
            } catch (e) {
                if (e.name !== 'AbortError') alert('No se pudo descargar el PDF. Verificá tu conexión.');
            }
            this.enviando = false;
        },
    }));
});

@if($colegio)
(function() {
    let conteo = {{ $estudiantes->filter(fn($e) => $e->resultados->isNotEmpty())->count() }};
    const banner = document.getElementById('nuevo-resultado-banner');
    setInterval(async function() {
        try {
            const r = await fetch('{{ route("admin.colegios.recuento", $colegio->id) }}');
            const d = await r.json();
            if (d.count > conteo) banner.classList.remove('hidden');
        } catch(e) {}
    }, 30000);
})();
@endif
</script>
@endpush
