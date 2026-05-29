{{-- Pie institucional centrado --}}
<footer class="mt-16">
  <div class="mx-auto max-w-4xl px-4 sm:px-6 pb-6">
    <div class="relative overflow-hidden rounded-3xl bg-ink text-white px-6 sm:px-10 py-10 shadow-float text-center">
      {{-- Acento dorado superior --}}
      <div class="absolute top-0 inset-x-0 h-1 bg-gold-500"></div>

      {{-- Logo --}}
      @if (file_exists(public_path('images/logo-jac.png')))
        <img src="{{ asset('images/logo-jac.png') }}" alt="JAC Boliviano 2000"
             class="mx-auto h-16 w-16 rounded-full object-contain bg-white p-1 ring-2 ring-gold-500/40 shadow-btn">
      @endif

      <p class="font-display font-bold text-2xl mt-4">JAC Boliviano 2000</p>
      <p class="text-xs uppercase tracking-[0.25em] text-gold-400 mt-1">Orientación Vocacional · CHASIDE</p>

      <p class="text-sm text-white/70 mt-4 leading-relaxed">
        Av. San Martín esq. Brasil, Edif. Pruber 901, 2do Piso<br>
        Cochabamba — Bolivia
      </p>

      {{-- Teléfonos --}}
      <div class="flex flex-wrap justify-center gap-2.5 mt-5">
        @foreach (['4553737', '71443907', '61624258'] as $tel)
          <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-4 py-2 text-sm font-medium">
            <svg class="w-3.5 h-3.5 text-gold-400" fill="currentColor" viewBox="0 0 24 24"><path d="M6.6 10.8c1.4 2.8 3.8 5.1 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1C10.6 21 3 13.4 3 4c0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.2.2 2.4.6 3.6.1.4 0 .8-.3 1l-2.2 2.2z"/></svg>
            {{ $tel }}
          </span>
        @endforeach
      </div>

      <div class="mt-7 pt-5 border-t border-white/10 text-[11px] text-white/45">
        © {{ date('Y') }} JAC Boliviano 2000 · Departamento de Orientación · RM. 2450/17
      </div>
    </div>
  </div>
</footer>
