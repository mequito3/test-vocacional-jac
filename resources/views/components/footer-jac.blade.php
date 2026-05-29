{{-- Pie minimalista (mismo estilo limpio del header) --}}
<footer class="mt-20 border-t border-slate-200/70">
  <div class="mx-auto max-w-4xl px-5 sm:px-6 py-8">
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
      <div class="flex items-center gap-3">
        @if (file_exists(public_path('images/logo-jac.png')))
          <img src="{{ asset('images/logo-jac.png') }}" alt="JAC Boliviano 2000" class="h-9 w-9 rounded-full object-contain">
        @endif
        <span class="leading-tight text-center sm:text-left">
          <span class="block font-display font-semibold text-ink">JAC Boliviano 2000</span>
          <span class="block text-xs text-slate-400">Av. San Martín esq. Brasil · Cochabamba — Bolivia</span>
        </span>
      </div>
      <div class="text-xs text-slate-500 tracking-wide">
        Tel. 4553737 · 71443907 · 61624258
      </div>
    </div>
    <div class="mt-6 pt-4 border-t border-slate-100 text-center text-[11px] text-slate-400">
      © {{ date('Y') }} JAC Boliviano 2000 · Departamento de Orientación · RM. 2450/17
    </div>
  </div>
</footer>
