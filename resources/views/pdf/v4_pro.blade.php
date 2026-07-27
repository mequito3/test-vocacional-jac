<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
@php
    $areaP = $areas[$principal];
    $areaS = $areas[$secundaria];
    $orden = $ordenAreas;
    $logo = public_path('images/logo-jac.png');
    $hasLogo = file_exists($logo);
    $color = $areaP['color'];

    $hex = ltrim($color, '#');
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    $onColor = (($r * 299 + $g * 587 + $b * 114) / 1000) > 145 ? '#16233f' : '#ffffff';

    $pctPrincipal = (int) round(($puntajes[$principal] / 14) * 100);
    $afinidad = $pctPrincipal >= 90 ? 'Afinidad muy alta' : ($pctPrincipal >= 75 ? 'Afinidad alta' : 'Afinidad favorable');

    $meses = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
    $fechaBase = $resultado->created_at ?? now();
    $fecha = $fechaBase->day . ' de ' . $meses[$fechaBase->month - 1] . ' de ' . $fechaBase->year;

    $carreras = array_values($areaP['carreras']);
    $carreraPrincipal = $carreras[0] ?? $areaP['nombre'];
    $carrerasCompatibles = array_slice($carreras, 1, 4);

    $descripciones = [
        'C' => 'El perfil evidencia capacidad para organizar información, administrar recursos y tomar decisiones con criterio. Presenta una inclinación clara hacia entornos de gestión, planificación y análisis de resultados.',
        'H' => 'El perfil muestra facilidad para comprender necesidades humanas, comunicar ideas y analizar contextos sociales. Destaca una orientación hacia el acompañamiento, la educación, la comunicación y el servicio social.',
        'A' => 'El perfil revela sensibilidad estética, imaginación y facilidad para expresar ideas de forma visual, sonora o creativa. La persona muestra interés por actividades donde la originalidad y la composición son importantes.',
        'S' => 'El perfil manifiesta vocación de servicio, empatía y disposición para cuidar el bienestar de otras personas. Existe inclinación hacia áreas vinculadas con salud, prevención, atención y responsabilidad humana.',
        'I' => 'El perfil refleja pensamiento lógico, capacidad de resolver problemas y gusto por comprender cómo funcionan los sistemas. Presenta afinidad con actividades técnicas, tecnológicas, constructivas y de innovación.',
        'D' => 'El perfil muestra disciplina, sentido de justicia, capacidad de reacción y orientación al servicio institucional. Se observa interés por seguridad, orden, liderazgo y trabajo bajo responsabilidad.',
        'E' => 'El perfil evidencia curiosidad científica, pensamiento analítico y gusto por investigar fenómenos naturales o exactos. Destaca la capacidad de observación, orden y razonamiento metódico.',
    ];

    $recomendaciones = [
        'C' => 'Fortalecer matemáticas aplicadas, comunicación empresarial, herramientas digitales, liderazgo y toma de decisiones.',
        'H' => 'Fortalecer lectura crítica, redacción, expresión oral, investigación social, escucha activa y análisis de realidad.',
        'A' => 'Fortalecer portafolio creativo, dibujo, composición visual, cultura artística y manejo de herramientas digitales.',
        'S' => 'Fortalecer biología, química, hábitos de estudio, responsabilidad, comunicación empática y trabajo colaborativo.',
        'I' => 'Fortalecer matemáticas, física, lógica, programación básica, análisis de problemas y constancia práctica.',
        'D' => 'Fortalecer disciplina personal, condición física, ética, normativa, comunicación bajo presión y trabajo en equipo.',
        'E' => 'Fortalecer matemáticas, ciencias naturales, método científico, laboratorio, observación y registro de datos.',
    ];

    $colegioNombre = optional($estudiante->colegio)->nombre;
@endphp
<style>
@page{size:A4;margin:0}
*{box-sizing:border-box}
body{margin:0;font-family:DejaVu Sans,sans-serif;color:#182235;background:#fff;font-size:11px}
.top{background:#0b1626;color:#fff;padding:15px 30px 13px;border-bottom:5px solid {{ $color }}}
.brand{display:table;width:100%}
.brand-left,.brand-right{display:table-cell;vertical-align:middle}
.brand-right{text-align:right;color:#b9c4d8;font-size:10px}
.logo{width:35px;height:35px;object-fit:contain;background:#fff;border-radius:7px;padding:4px;margin-right:9px;vertical-align:middle}
.brand-title{font-size:16px;font-weight:700;display:inline-block;vertical-align:middle}
.brand-sub{font-size:10px;color:#b9c4d8;margin-top:2px}
.page{padding:20px 30px 50px}
.eyebrow{font-size:10px;text-transform:uppercase;letter-spacing:1.7px;color:#64748b;font-weight:700}
.hero{padding:0 0 12px;border-bottom:1px solid #e5eaf1}
.hero-table{width:100%;border-collapse:collapse}
.hero-main{width:67%;vertical-align:middle;padding-right:16px}
.hero-score{width:33%;vertical-align:middle;text-align:center;border-left:1px solid #e5eaf1}
.student{font-size:24px;line-height:1.1;font-weight:800;color:#0f172a;margin:6px 0 6px}
.meta{font-size:11px;color:#64748b;line-height:1.45}
.pill{display:inline-block;background:{{ $color }};color:{{ $onColor }};border-radius:4px;padding:6px 9px;font-weight:700;font-size:11px;margin-top:8px}
.score-big{font-size:52px;line-height:1;font-weight:800;color:{{ $color }}}
.score-label{font-size:12px;font-weight:700;color:#0f172a;margin-top:3px}
.career-box{margin-top:10px;background:#f8fafc;border-left:5px solid {{ $color }};padding:9px 12px}
.career-title{font-size:10px;font-weight:800;color:#64748b;text-transform:uppercase;letter-spacing:1.3px}
.career{font-size:23px;font-weight:800;color:#0f172a;margin-top:3px;line-height:1.12}
.related{margin-top:6px;border:1px solid #e5eaf1;background:#fff;padding:6px 10px}
.related .title{font-size:10px;margin-bottom:13px}
.related span{display:inline-block;border:1px solid #e2e8f0;border-radius:3px;padding:5px 8px;margin:0 7px 7px 0;color:#334155;background:#f8fafc;font-size:10px;line-height:1.25}
.grid{width:100%;border-collapse:collapse;margin-top:10px}
.col{width:50%;vertical-align:top}
.box{border:1px solid #e5eaf1;border-top:4px solid {{ $color }};padding:10px 12px;min-height:96px}
.box.gold{border-top-color:#c9a14a}
.title{font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:1px;margin-bottom:7px;color:#0f172a}
.copy{line-height:1.52;color:#334155}
.list{margin:0;padding-left:16px;line-height:1.5}
.scores{margin-top:10px;border:1px solid #e5eaf1;padding:10px 12px}
.score-table{width:100%;border-collapse:collapse}
.score-table td{padding:3px 0;vertical-align:middle}
.score-name{width:210px;color:#334155}
.dot{display:inline-block;width:9px;height:9px;margin-right:7px;border-radius:2px}
.bar{height:9px;background:#edf2f7;border-radius:3px;overflow:hidden}
.bar div{height:9px;border-radius:3px}
.score-pct{width:42px;text-align:right;color:#0f172a}
.footer{position:fixed;left:0;right:0;bottom:0;height:30px;background:#0b1626;color:#aab6ca;text-align:center;font-size:9px;line-height:1.2;padding:8px 26px 0;border-top:3px solid #c9a14a;overflow:hidden;white-space:nowrap}
.note{margin-top:7px;background:#fff;border:1px solid #e5eaf1;padding:7px 10px;color:#334155;line-height:1.34;font-size:10.5px}
</style>
</head>
<body>
<div class="top">
  <div class="brand">
    <div class="brand-left">
      @if($hasLogo)<img src="{{ $logo }}" class="logo">@endif
      <div class="brand-title">JAC Bolivia 2000<div class="brand-sub">Orientación Vocacional CHASIDE</div></div>
    </div>
    <div class="brand-right">Informe de Resultados<br>{{ $fecha }}</div>
  </div>
</div>

<div class="page">
  <div class="hero">
    <table class="hero-table">
      <tr>
        <td class="hero-main">
          <div class="eyebrow">Estudiante</div>
          <div class="student">{{ $estudiante->nombre_completo }}</div>
          <div class="meta">
            {{ $estudiante->sexo }} · {{ $estudiante->edad }} años
            @if($colegioNombre)<br>{{ $colegioNombre }}@endif
          </div>
          <span class="pill">Área dominante: {{ $areaP['nombre'] }}</span>
        </td>
        <td class="hero-score">
          <div class="score-big">{{ $pctPrincipal }}%</div>
          <div class="score-label">{{ $afinidad }}</div>
          <div style="color:#64748b;margin-top:5px">{{ $puntajes[$principal] }} de 14 puntos</div>
        </td>
      </tr>
    </table>
  </div>

  <div class="career-box">
    <div class="career-title">Carrera sugerida principal</div>
    <div class="career">{{ $carreraPrincipal }}</div>
  </div>

  @if(count($carrerasCompatibles))
  <div class="related">
    <div class="title">Otras carreras compatibles</div>
    @foreach($carrerasCompatibles as $carrera)
      <span>{{ $carrera }}</span>
    @endforeach
  </div>
  @endif

  <table class="grid">
    <tr>
      <td class="col" style="padding-right:8px">
        <div class="box">
          <div class="title">Perfil vocacional</div>
          <div class="copy">{{ $descripciones[$principal] }}</div>
        </div>
      </td>
      <td class="col" style="padding-left:8px">
        <div class="box gold">
          <div class="title">Recomendación de preparación</div>
          <div class="copy">{{ $recomendaciones[$principal] }}</div>
        </div>
      </td>
    </tr>
  </table>

  <table class="grid">
    <tr>
      <td class="col" style="padding-right:8px">
        <div class="box">
          <div class="title">Intereses destacados</div>
          <ul class="list">
            @foreach($areaP['intereses'] as $item)<li>{{ $item }}</li>@endforeach
          </ul>
        </div>
      </td>
      <td class="col" style="padding-left:8px">
        <div class="box gold">
          <div class="title">Aptitudes destacadas</div>
          <ul class="list">
            @foreach($areaP['aptitudes'] as $item)<li>{{ $item }}</li>@endforeach
          </ul>
        </div>
      </td>
    </tr>
  </table>

  <div class="scores">
    <div class="title">Puntajes por área</div>
    <table class="score-table">
      @foreach($orden as $key)
        @php
          $pct = (int) round(($puntajes[$key] / 14) * 100);
          $barColor = $areas[$key]['color'];
          $weight = $key === $principal ? '700' : '400';
        @endphp
        <tr>
          <td class="score-name"><span class="dot" style="background:{{ $barColor }}"></span>{{ $areas[$key]['nombre'] }}</td>
          <td><div class="bar"><div style="width:{{ $pct }}%;background:{{ $barColor }}"></div></div></td>
          <td class="score-pct" style="font-weight:{{ $weight }}">{{ $pct }}%</td>
        </tr>
      @endforeach
    </table>
  </div>

  <div class="note">
    <b>Interpretación:</b> el resultado orienta principalmente hacia <b>{{ $carreraPrincipal }}</b>, manteniendo como segunda línea de afinidad el área de <b>{{ $areaS['nombre'] }}</b>. La elección final debe complementarse con entrevista, rendimiento académico e intereses personales.
  </div>
</div>

<div class="footer">JAC Bolivia 2000 · Av. San Martín esq. Brasil, Ed. Pruber 901 · Cochabamba, Bolivia · Tel. 4553737 · 7144390</div>
</body>
</html>
