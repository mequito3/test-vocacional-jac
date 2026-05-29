<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Test Vocacional CHASIDE') — JAC Boliviano 2000</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,400;12..96,500;12..96,600;12..96,700;12..96,800&family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&display=swap" rel="stylesheet">

<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = {
    theme: {
      extend: {
        colors: {
          ink:    '#16233f',
          navy:   { 700:'#1b3a6b', 600:'#234c8a', 500:'#2e63ad' },
          gold:   { 500:'#f5a623', 400:'#ffbd4a', 600:'#dc8f12' },
          cloud:  '#eef3fb',
        },
        fontFamily: {
          display: ['"Bricolage Grotesque"', 'sans-serif'],
          sans:    ['"DM Sans"', 'system-ui', 'sans-serif'],
        },
        boxShadow: {
          card: '0 18px 40px -22px rgba(27,58,107,.45)',
          float:'0 24px 60px -24px rgba(27,58,107,.55)',
          btn:  '0 10px 24px -8px rgba(27,58,107,.6)',
          gold: '0 12px 28px -10px rgba(245,166,35,.7)',
        },
        borderRadius: { '2xl':'1.25rem', '3xl':'1.75rem', '4xl':'2.25rem' },
      },
    },
  };
</script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@stack('head')

<style>
  body{font-family:'DM Sans',system-ui,sans-serif;}
  .font-display{font-family:'Bricolage Grotesque',sans-serif;}
  [x-cloak]{display:none!important}
  ::selection{background:#f5a623;color:#16233f}
  ::-webkit-scrollbar{width:11px}
  ::-webkit-scrollbar-thumb{background:#c4d3ec;border-radius:9px;border:3px solid transparent;background-clip:content-box}

  /* Atmosfera de fondo: glows suaves + textura sutil */
  .bg-atmos{
    background-color:#f3f7fd;
    background-image:
      radial-gradient(820px 520px at 88% -8%, rgba(245,166,35,.16), transparent 60%),
      radial-gradient(720px 600px at -10% 8%, rgba(46,99,173,.16), transparent 55%),
      radial-gradient(680px 680px at 50% 120%, rgba(27,58,107,.10), transparent 60%);
  }
  .blob{position:fixed;border-radius:50%;filter:blur(70px);opacity:.5;z-index:0;pointer-events:none}

  /* Aparicion escalonada al cargar */
  @keyframes rise{from{opacity:0;transform:translateY(18px)}to{opacity:1;transform:none}}
  .rise{opacity:0;animation:rise .6s cubic-bezier(.2,.7,.2,1) forwards}
  .d1{animation-delay:.05s}.d2{animation-delay:.13s}.d3{animation-delay:.21s}.d4{animation-delay:.29s}.d5{animation-delay:.37s}.d6{animation-delay:.45s}

  @keyframes floaty{0%,100%{transform:translateY(0)}50%{transform:translateY(-10px)}}
  .floaty{animation:floaty 6s ease-in-out infinite}
  .card{background:rgba(255,255,255,.82);backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,.7)}
</style>
</head>
<body class="bg-atmos text-ink min-h-screen flex flex-col overflow-x-hidden">

  <div class="blob floaty" style="width:300px;height:300px;background:#ffce7a;top:-60px;right:-40px"></div>
  <div class="blob" style="width:340px;height:340px;background:#7fa9e6;bottom:-120px;left:-80px"></div>

  <div class="relative z-10 flex flex-col min-h-screen">
    <x-header-jac />
    <main class="flex-1 w-full">
      @yield('content')
    </main>
    <x-footer-jac />
  </div>

  @stack('scripts')
</body>
</html>
