{{-- Cabecera institucional centrada --}}
<header class="sticky top-0 z-30">
  <div class="mx-auto max-w-4xl px-4 sm:px-6 mt-3 sm:mt-4">
    <div class="card shadow-card rounded-2xl px-5 py-3 relative">
      <div class="absolute top-0 inset-x-0 h-0.5 bg-gold-500/70 rounded-t-2xl"></div>
      <a href="{{ route('welcome') }}" class="flex items-center justify-center gap-3">
        @if (file_exists(public_path('images/logo-jac.png')))
          <img src="{{ asset('images/logo-jac.png') }}" alt="JAC Boliviano 2000" class="h-11 w-11 rounded-full object-contain bg-white p-0.5 ring-1 ring-slate-200">
        @else
          <span class="h-11 w-11 grid place-items-center rounded-full bg-ink text-gold-400 font-display font-extrabold">J</span>
        @endif
        <span class="text-center leading-tight">
          <span class="block font-display font-bold text-ink text-lg">JAC Boliviano 2000</span>
          <span class="block text-[11px] uppercase tracking-[0.2em] text-navy-600/70">Orientación Vocacional · CHASIDE</span>
        </span>
      </a>
    </div>
  </div>
</header>
