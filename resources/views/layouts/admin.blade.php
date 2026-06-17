<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', 'Admin') — JAC Bolivia 2000</title>
<link rel="icon" type="image/png" href="{{ asset('images/logo-jac.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600;12..96,700;12..96,800&family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = {
    theme: {
      extend: {
        colors: {
          ink:  '#16233f',
          navy: { 700:'#1b3a6b', 600:'#234c8a', 500:'#2e63ad', 50:'#eef4ff' },
          gold: { 500:'#f5a623', 600:'#dc8f12' },
        },
        fontFamily: {
          display: ['"Bricolage Grotesque"', 'sans-serif'],
          sans:    ['"DM Sans"', 'system-ui', 'sans-serif'],
        },
      },
    },
  };
</script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<style>
  body { font-family: 'DM Sans', system-ui, sans-serif; }
  .font-display { font-family: 'Bricolage Grotesque', sans-serif; }
  [x-cloak] { display: none !important; }
</style>
</head>
<body class="bg-slate-50 text-ink min-h-screen flex flex-col">

  {{-- Top bar --}}
  <header class="bg-navy-700 text-white shadow-lg">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <img src="{{ asset('images/logo-jac.png') }}" alt="JAC" class="w-8 h-8 rounded-lg object-contain bg-white/10 p-1">
        <div class="leading-tight">
          <span class="font-display font-bold text-base block">JAC Bolivia 2000</span>
          <span class="text-white/55 text-xs">Panel de Administración</span>
        </div>
      </div>
      <div class="flex items-center gap-4">
        <a href="{{ route('welcome') }}" target="_blank"
           class="text-white/60 hover:text-white text-sm transition-colors hidden sm:inline">
          Ver sitio
        </a>
        <form method="POST" action="{{ route('admin.logout') }}">
          @csrf
          <button class="flex items-center gap-1.5 text-white/80 hover:text-white text-sm transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            Salir
          </button>
        </form>
      </div>
    </div>
  </header>

  <main class="flex-1 max-w-6xl mx-auto w-full px-4 sm:px-6 py-8">
    @yield('content')
  </main>

  <footer class="text-center text-xs text-slate-400 py-4">
    Test Vocacional CHASIDE · JAC Bolivia 2000
  </footer>

  @stack('scripts')
</body>
</html>
