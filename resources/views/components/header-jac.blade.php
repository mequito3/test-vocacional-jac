{{-- Cabecera minimalista (mismo criterio que el footer: identidad izq. + dato der.) --}}
<header class="sticky top-0 z-30 bg-white/75 backdrop-blur-md border-b border-slate-200/70">
  <x-container class="h-16 flex items-center justify-between gap-4">
    <a href="{{ route('welcome') }}" class="flex items-center gap-3">
      @if (file_exists(public_path('images/logo-jac.png')))
        <img src="{{ asset('images/logo-jac.png') }}" alt="JAC Boliviano 2000" class="h-9 w-9 rounded-full object-contain">
      @else
        <span class="h-9 w-9 grid place-items-center rounded-full bg-ink text-gold-400 font-display font-bold text-sm">J</span>
      @endif
      <span class="leading-tight">
        <span class="block font-display font-semibold text-ink text-[17px] tracking-tight">JAC Boliviano 2000</span>
        <span class="block text-xs text-slate-400">Orientación Vocacional</span>
      </span>
    </a>
    <span class="hidden sm:block text-[11px] uppercase tracking-[0.22em] text-slate-400">Método CHASIDE</span>
  </x-container>
</header>
