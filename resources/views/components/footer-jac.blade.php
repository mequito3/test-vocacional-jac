{{-- Pie moderno con datos de contacto --}}
<footer class="mt-16">
  <div class="mx-auto max-w-6xl px-4 sm:px-6 pb-6">
    <div class="rounded-3xl bg-navy-700 text-white px-6 sm:px-9 py-8 shadow-float relative overflow-hidden">
      <div class="absolute -top-10 -right-10 w-40 h-40 rounded-full bg-gold-500/20 blur-2xl"></div>
      <div class="relative grid gap-6 sm:grid-cols-2 items-start">
        <div>
          <p class="font-display font-bold text-xl">JAC Boliviano 2000</p>
          <p class="text-sm text-white/70 mt-2 leading-relaxed">
            Av. San Martín esq. Brasil, Edif. Pruber 901, 2do Piso<br>
            Cochabamba — Bolivia
          </p>
        </div>
        <div class="sm:text-right">
          <p class="text-xs uppercase tracking-[0.2em] text-gold-400 font-semibold mb-2">Contáctanos</p>
          <div class="flex sm:justify-end flex-wrap gap-2">
            @foreach (['4553737','71443907','61624258'] as $tel)
              <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1.5 text-sm font-medium">
                <svg class="w-3.5 h-3.5 text-gold-400" fill="currentColor" viewBox="0 0 24 24"><path d="M6.6 10.8c1.4 2.8 3.8 5.1 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1C10.6 21 3 13.4 3 4c0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.2.2 2.4.6 3.6.1.4 0 .8-.3 1l-2.2 2.2z"/></svg>
                {{ $tel }}
              </span>
            @endforeach
          </div>
        </div>
      </div>
      <div class="relative mt-7 pt-5 border-t border-white/10 text-[11px] text-white/45">
        © {{ date('Y') }} JAC Boliviano 2000 · Departamento de Orientación · Método CHASIDE
      </div>
    </div>
  </div>
</footer>
