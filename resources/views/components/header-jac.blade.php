{{-- Cabecera moderna JAC Bolivia 2000 --}}
<header class="sticky top-0 z-30">
  <div class="mx-auto max-w-6xl px-4 sm:px-6 mt-3 sm:mt-4">
    <div class="card shadow-card rounded-2xl px-4 sm:px-5 py-3 flex items-center gap-3">
      <a href="{{ route('welcome') }}" class="flex items-center gap-3 min-w-0">
        @if (file_exists(public_path('images/logo-jac.png')))
          <img src="{{ asset('images/logo-jac.png') }}" alt="JAC Bolivia 2000" class="h-12 w-12 rounded-full object-contain bg-white p-0.5 ring-1 ring-slate-200 shadow-btn">
        @else
          <span class="h-12 w-12 shrink-0 grid place-items-center rounded-full bg-navy-700 text-gold-400 font-display font-extrabold text-lg shadow-btn">J</span>
        @endif
        <span class="leading-tight min-w-0">
          <span class="block font-display font-bold text-ink text-[15px] sm:text-base truncate">JAC Bolivia 2000</span>
          <span class="block text-[11px] sm:text-xs text-navy-600/70 font-medium truncate">Orientación Vocacional · CHASIDE</span>
        </span>
      </a>
      <span class="ml-auto hidden sm:inline-flex items-center gap-1.5 rounded-full bg-gold-500/15 text-gold-600 text-xs font-semibold px-3 py-1.5">
        <span class="w-1.5 h-1.5 rounded-full bg-gold-500"></span> Instituto Técnico Superior
      </span>
    </div>
  </div>
</header>
