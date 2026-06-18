@props([
    'show'        => 'modalFin',
    'onConfirm'   => "limpiarGuardado(); \$refs.btnEnviar.click()",
    'onRevisar'   => 'modalFin = false',
    'total'       => 98,
])
{{--
    Modal que aparece al completar el test CHASIDE.
    Depende del scope Alpine del padre:
      - variable booleana indicada en :show (default: modalFin)
      - $refs.btnEnviar para enviar el formulario
--}}
<template x-teleport="body">
  <div x-show="{{ $show }}"
       x-cloak
       x-transition:enter="transition ease-out duration-300"
       x-transition:enter-start="opacity-0"
       x-transition:enter-end="opacity-100"
       x-transition:leave="transition ease-in duration-200"
       x-transition:leave-start="opacity-100"
       x-transition:leave-end="opacity-0"
       style="position:fixed;inset:0;z-index:9999;"
       class="flex items-center justify-center p-4 bg-ink/70">

    <div class="w-full max-w-sm rounded-4xl shadow-float overflow-hidden" @click.stop>

      {{-- Cabecera navy --}}
      <div class="bg-navy-700 px-8 pt-8 pb-9 text-center relative overflow-hidden">
        <div class="absolute -top-8 -right-8 w-36 h-36 rounded-full bg-white/5"></div>
        <div class="absolute -bottom-6 -left-6 w-24 h-24 rounded-full bg-white/5"></div>
        <div class="absolute top-0 left-0 right-0 h-0.5 bg-gradient-to-r from-transparent via-gold-500/60 to-transparent"></div>

        <img src="{{ asset('images/logo-jac.png') }}" alt="JAC"
             class="relative w-14 h-14 rounded-xl object-contain mx-auto mb-4"
             style="background:rgba(255,255,255,.12);padding:7px;">

        <div class="relative flex items-center justify-center gap-2 mb-4">
          <span class="dot"></span><span class="dot"></span><span class="dot"></span><span class="dot"></span><span class="dot"></span>
        </div>

        <p class="relative text-white/45 text-[10px] font-bold uppercase tracking-[0.28em] mb-2">Analizando tu perfil</p>
        <h2 class="relative font-display font-extrabold text-white leading-none" style="font-size:2.6rem;">
          ¡Todo listo!
        </h2>

        <div class="relative inline-flex items-baseline gap-1 mt-3 bg-white/10 rounded-2xl px-5 py-2">
          <span class="font-display font-extrabold text-gold-500 text-3xl leading-none">{{ $total }}</span>
          <span class="text-white/40 font-bold text-lg">/</span>
          <span class="font-display font-bold text-white/60 text-lg leading-none">{{ $total }}</span>
          <span class="text-white/40 text-xs ml-1.5 font-semibold uppercase tracking-wider">preguntas</span>
        </div>
      </div>

      {{-- Cuerpo blanco --}}
      <div class="bg-white px-7 py-6 text-center">
        <p class="text-slate-500 text-sm leading-relaxed mb-5">
          Tu orientador de <strong class="text-ink">JAC Bolivia 2000</strong> revisará tu perfil
          y te enviará el informe vocacional personalizado por <strong class="text-ink">WhatsApp</strong>.
        </p>

        <button type="button" @click="{{ $onConfirm }}"
          class="group w-full inline-flex items-center justify-center gap-2.5 rounded-2xl bg-navy-700 hover:bg-navy-600 text-white font-bold text-base px-8 py-4 shadow-btn hover:-translate-y-0.5 transition-all">
          ¡Entendido!
          <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
          </svg>
        </button>

        <button type="button" @click="{{ $onRevisar }}"
          class="mt-4 text-sm text-slate-400 hover:text-slate-600 transition-colors">
          ← Revisar mis respuestas
        </button>
      </div>

    </div>
  </div>
</template>
