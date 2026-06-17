<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Acceso Admin — JAC Bolivia 2000</title>
<link rel="icon" type="image/png" href="{{ asset('images/logo-jac.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,700;12..96,800&family=DM+Sans:opsz,wght@9..40,400;9..40,600&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = {
    theme: {
      extend: {
        colors: {
          ink:  '#16233f',
          navy: { 700:'#1b3a6b', 600:'#234c8a' },
          gold: { 500:'#f5a623' },
        },
        fontFamily: {
          display: ['"Bricolage Grotesque"', 'sans-serif'],
          sans:    ['"DM Sans"', 'system-ui'],
        },
      },
    },
  };
</script>
<style>body { font-family: 'DM Sans', system-ui; }</style>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center px-4">

  <div class="w-full max-w-sm">
    <div class="text-center mb-8">
      <img src="{{ asset('images/logo-jac.png') }}" alt="JAC" class="w-16 h-16 mx-auto rounded-2xl shadow-md object-contain bg-white p-2 mb-4">
      <h1 class="font-display font-extrabold text-ink text-3xl">Panel Admin</h1>
      <p class="text-slate-500 text-sm mt-1">JAC Bolivia 2000 · Test Vocacional</p>
    </div>

    <div class="bg-white rounded-3xl shadow-xl border border-slate-200/70 p-7">
      @if ($errors->any())
        <div class="mb-4 rounded-2xl bg-rose-50 border border-rose-200 px-4 py-3 text-sm text-rose-700">
          {{ $errors->first() }}
        </div>
      @endif

      <form method="POST" action="{{ route('admin.login.post') }}">
        @csrf
        <label class="block text-sm font-semibold text-slate-600 mb-1.5">Contraseña de acceso</label>
        <input type="password" name="password" autofocus required
               class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-ink focus:border-navy-500 focus:ring-4 focus:ring-navy-500/10 outline-none transition @error('password') border-rose-400 @enderror">
        <button type="submit"
                class="w-full mt-5 rounded-2xl bg-navy-700 hover:bg-navy-600 text-white font-semibold py-4 transition-colors">
          Ingresar al panel
        </button>
      </form>
    </div>

    <p class="text-center text-xs text-slate-400 mt-6">
      <a href="{{ route('welcome') }}" class="hover:text-slate-600 transition-colors">← Volver al sitio</a>
    </p>
  </div>

</body>
</html>
