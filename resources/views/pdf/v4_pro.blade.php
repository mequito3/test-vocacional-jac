<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
@php
    $areaP   = $areas[$principal];
    $areaS   = $areas[$secundaria];
    $logo    = public_path('images/logo-jac.png');
    $hasLogo = file_exists($logo);
    $half    = (int) ceil(count($areaP['carreras']) / 2);
    $color   = $areaP['color'];

    $hex = ltrim($color,'#');
    $r = hexdec(substr($hex,0,2));
    $g = hexdec(substr($hex,2,2));
    $b = hexdec(substr($hex,4,2));
    $lum     = ($r*299 + $g*587 + $b*114) / 1000;
    $onColor = $lum > 145 ? '#1e293b' : '#ffffff';
    $pale    = sprintf('rgba(%d,%d,%d,0.07)', $r, $g, $b);

    /* Porcentajes */
    $pctPrincipal = round(($puntajes[$principal] / 14) * 100);
    $pctSec       = round(($puntajes[$secundaria] / 14) * 100);

    /* Etiqueta dinámica de afinidad */
    if ($pctPrincipal >= 86)      $afinidad = 'Afinidad muy alta';
    elseif ($pctPrincipal >= 71)  $afinidad = 'Afinidad alta';
    elseif ($pctPrincipal >= 57)  $afinidad = 'Afinidad media';
    else                          $afinidad = 'Afinidad moderada';

    $mes   = ['enero','febrero','marzo','abril','mayo','junio',
              'julio','agosto','septiembre','octubre','noviembre','diciembre'];
    $fecha = $resultado->created_at->day.' de '
           . $mes[$resultado->created_at->month-1].' de '
           . $resultado->created_at->year;

    $txt = [
        'C' => 'Orientación hacia la gestión, el análisis numérico y la organización de recursos. Aptitud para entornos donde la planificación estratégica y la toma de decisiones son el eje profesional.',
        'H' => 'Inclinación hacia las ciencias sociales, la comunicación y el trabajo con personas. Capacidad para comprender la realidad social y contribuir al bienestar colectivo desde disciplinas humanísticas.',
        'A' => 'Sensibilidad estética, creatividad y pensamiento divergente. Aptitud para generar propuestas originales en lenguajes visuales, sonoros y expresivos de alto impacto comunicativo.',
        'S' => 'Afinidad con el cuidado de la salud y la atención a las personas. Vocación de servicio, empatía profunda y disposición para responsabilidades de alto impacto en el bienestar humano.',
        'I' => 'Orientación hacia el razonamiento técnico y la resolución de problemas. Aptitud analítica y práctica para el diseño, construcción y optimización de sistemas, procesos y tecnologías.',
        'D' => 'Vocación por la seguridad, la defensa y el trabajo bajo presión. Liderazgo, valentía y compromiso con la justicia y el orden institucional en situaciones de alta exigencia.',
        'E' => 'Orientación hacia la investigación científica y el razonamiento lógico-matemático. Meticulosidad y aptitud para el trabajo experimental y el análisis riguroso de fenómenos naturales.',
    ];

    /* Divide nombre_completo en máximo 2 líneas para evitar desbordamiento */
    $nameParts = array_values(array_filter(explode(' ', trim($estudiante->nombre_completo))));
    $wc    = count($nameParts);
    $split = max(1, (int)ceil($wc / 2));
    $nameL1 = implode(' ', array_slice($nameParts, 0, $split));
    $nameL2 = implode(' ', array_slice($nameParts, $split));

    $frases = [
        'C' => 'Tu perfil muestra una destacada capacidad para organizar, planificar y tomar decisiones con criterio. Esas habilidades son la base de quienes lideran equipos, gestionan proyectos y construyen resultados concretos dentro de las organizaciones.',
        'H' => 'Tu perfil refleja una capacidad genuina para comprender, comunicar e interpretar el mundo desde las personas. Quienes destacan en estas áreas tienen el poder de transformar realidades sociales, educativas y culturales con su visión.',
        'A' => 'Tu perfil evidencia una creatividad y sensibilidad que van más allá de lo técnico. Las personas con tu tipo de perfil tienen la capacidad de comunicar ideas de forma original y generar impacto donde la expresión y el diseño son protagonistas.',
        'S' => 'Tu perfil refleja vocación de servicio, empatía y un compromiso genuino con el bienestar de las personas. Quienes eligen este camino encuentran en cada acción una oportunidad de generar un impacto real y profundo en la vida de otros.',
        'I' => 'Tu perfil muestra una destacada capacidad para analizar problemas, identificar patrones y construir soluciones. Esas habilidades son el motor de la innovación tecnológica y la base de quienes crean las herramientas que transforman el mundo.',
        'D' => 'Tu perfil refleja disciplina, responsabilidad y la capacidad de actuar con decisión cuando más se necesita. Quienes tienen estas cualidades encuentran en el liderazgo institucional y el servicio un propósito sólido y duradero.',
        'E' => 'Tu perfil evidencia un pensamiento lógico y analítico orientado a entender cómo funciona el mundo. Quienes eligen este camino tienen la capacidad de contribuir con conocimiento que trasciende generaciones y abre nuevas fronteras.',
    ];
    $frase = $frases[$principal];

    $PAD = 22;
    $FTR = 20;
@endphp
<style>
@page { size:A4 portrait; margin:0; }
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:'DejaVu Sans',sans-serif; font-size:11px; color:#1e293b; background:#fff; }

#foot-line { position:fixed; bottom:{{ $FTR }}px; left:0; right:0; height:3px; background:#c9a14a; }
#foot-bar  { position:fixed; bottom:0; left:0; right:0; height:{{ $FTR }}px;
             background:#0b1626; text-align:center; color:rgba(255,255,255,.55);
             font-size:10px; letter-spacing:.4px; padding-top:5px; overflow:hidden; }
#page      { margin-bottom:{{ $FTR + 8 }}px; }
</style>
</head>
<body>

<div id="foot-line"></div>
<div id="foot-bar">JAC BOLIVIA 2000 &nbsp;·&nbsp; Av. San Martín esq. Brasil, Ed. Pruber 901 &nbsp;·&nbsp; Cochabamba — Bolivia &nbsp;·&nbsp; Tel. 4553737 · 71443907</div>

<div id="page">

{{-- ══ HEADER ══════════════════════════════════════════════════════════ --}}
<table style="width:100%;border-collapse:collapse;background:#0b1626;">
  <tr>
    <td style="padding:10px {{ $PAD }}px 9px;">
      <table style="width:100%;border-collapse:collapse;">
        <tr>
          <td style="vertical-align:middle;">
            <table style="border-collapse:collapse;">
              <tr>
                @if($hasLogo)
                <td style="padding-right:9px;vertical-align:middle;">
                  <img src="{{ $logo }}" style="width:26px;height:26px;display:block;">
                </td>
                @endif
                <td style="vertical-align:middle;">
                  <div style="font-size:13px;font-weight:bold;color:#fff;">JAC Bolivia 2000</div>
                  <div style="font-size:11px;color:rgba(255,255,255,.4);letter-spacing:.5px;">Orientación Vocacional · CHASIDE</div>
                </td>
              </tr>
            </table>
          </td>
          <td style="text-align:right;vertical-align:middle;">
            <div style="font-size:11px;color:rgba(255,255,255,.4);">Informe de Resultados</div>
            <div style="font-size:11px;font-weight:bold;color:#c9a14a;margin-top:2px;">{{ $fecha }}</div>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<div style="height:4px;background:{{ $color }};"></div>
<div style="height:2px;background:#c9a14a;"></div>

{{-- ══ HERO ════════════════════════════════════════════════════════════ --}}
<div style="padding:13px {{ $PAD }}px 11px;background:#fff;page-break-inside:avoid;">
  <table style="width:100%;border-collapse:collapse;table-layout:fixed;">
    <colgroup>
      <col style="width:57%;">
      <col style="width:43%;">
    </colgroup>
    <tr>
      {{-- Datos del estudiante — columna fija al 57% para que el badge no se mueva --}}
      <td style="vertical-align:middle;padding-right:14px;">
        <div style="font-size:11px;font-weight:bold;color:#94a3b8;text-transform:uppercase;letter-spacing:2px;margin-bottom:5px;">Estudiante</div>
        <div style="font-size:24px;font-weight:bold;color:#0f172a;line-height:1.15;letter-spacing:-.2px;">{{ $nameL1 }}</div>
        @if($nameL2)
        <div style="font-size:24px;font-weight:bold;color:#0f172a;line-height:1.15;letter-spacing:-.2px;margin-top:2px;">{{ $nameL2 }}</div>
        @endif
        <div style="font-size:11px;color:#64748b;margin-top:6px;">
          {{ $estudiante->sexo }} &nbsp;&middot;&nbsp; {{ $estudiante->edad }} años
          @if(isset($estudiante->colegio) && $estudiante->colegio) &nbsp;&middot;&nbsp; {{ $estudiante->colegio->nombre }}@endif
        </div>
      </td>

      {{-- Área dominante: info + badge --}}
      <td style="vertical-align:middle;text-align:right;">
        <table style="border-collapse:collapse;margin-left:auto;">
          <tr>
            {{-- Texto informativo a la derecha del badge --}}
            <td style="vertical-align:middle;text-align:right;padding-right:13px;">
              <div style="font-size:11px;font-weight:bold;color:#94a3b8;text-transform:uppercase;letter-spacing:1.5px;margin-bottom:4px;">Área Dominante</div>
              <div style="font-size:16px;font-weight:bold;color:#0f172a;margin-bottom:7px;">{{ $areaP['nombre'] }}</div>
              {{-- Segunda área sin puntaje fraccionado --}}
              <div style="font-size:11px;color:#64748b;">Segunda área con mayor afinidad:</div>
              <div style="font-size:11px;font-weight:bold;color:#334155;margin-top:1px;">{{ $areaS['nombre'] }}</div>
              <div style="font-size:11px;color:#94a3b8;margin-top:1px;">{{ $pctSec }}% de afinidad</div>
            </td>
            {{-- Badge: solo porcentaje + etiqueta --}}
            <td style="vertical-align:middle;">
              <div style="width:80px;background:{{ $color }};text-align:center;padding:14px 4px 12px;border-radius:5px;">
                <div style="font-size:33px;font-weight:bold;color:{{ $onColor }};line-height:1;letter-spacing:-1px;">{{ $pctPrincipal }}%</div>
                <div style="font-size:11px;color:rgba(255,255,255,.7);margin-top:5px;line-height:1.3;">{{ $afinidad }}</div>
              </div>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</div>
<div style="height:1px;background:#e2e8f0;"></div>

{{-- ══ PERFIL VOCACIONAL (ancho completo) ══════════════════════════════ --}}
<div style="padding:10px {{ $PAD }}px 10px;page-break-inside:avoid;">
  <table style="border-collapse:collapse;margin-bottom:7px;">
    <tr>
      <td style="width:3px;background:{{ $color }};padding:0;"></td>
      <td style="padding-left:8px;">
        <div style="font-size:12px;font-weight:bold;color:#0f172a;text-transform:uppercase;letter-spacing:1px;">Perfil Vocacional</div>
      </td>
    </tr>
  </table>
  <div style="background:{{ $pale }};border-left:3px solid {{ $color }};padding:9px 14px;">
    <div style="font-size:11px;color:#1e3a5f;line-height:1.7;font-style:italic;">{{ $txt[$principal] }}</div>
  </div>
</div>
<div style="height:1px;background:#e2e8f0;"></div>

{{-- ══ PUNTAJES POR ÁREA (ancho completo, grid 3 col perfectamente alineado) ══ --}}
<div style="padding:10px {{ $PAD }}px 10px;page-break-inside:avoid;">
  <table style="border-collapse:collapse;margin-bottom:8px;">
    <tr>
      <td style="width:3px;background:{{ $color }};padding:0;"></td>
      <td style="padding-left:8px;">
        <div style="font-size:12px;font-weight:bold;color:#0f172a;text-transform:uppercase;letter-spacing:1px;">Puntajes por Área</div>
      </td>
    </tr>
  </table>

  {{-- Grilla fija: [nombre 170px] [barra auto] [porcentaje 34px] --}}
  <table style="width:100%;border-collapse:collapse;table-layout:fixed;">
    <colgroup>
      <col style="width:170px;">
      <col>
      <col style="width:34px;">
    </colgroup>
    @foreach($ordenAreas as $l)
      @php
        $isTop    = ($l === $principal);
        $isSec    = ($l === $secundaria);
        $pts      = $puntajes[$l];
        $pct      = round(($pts / 14) * 100);
        $colBar   = $areas[$l]['color'];
        $opBar    = $isTop ? '1' : ($isSec ? '0.55' : '0.28');
        $bH       = $isTop ? 11 : 8;
        $rowPad   = $isTop ? '4px' : '3px';
        /* Colores de texto según relevancia */
        $lblColor = $isTop ? '#0f172a' : ($isSec ? '#334155' : '#64748b');
        $pctColor = $isTop ? $colBar   : ($isSec ? '#475569' : '#64748b');
        $fw       = ($isTop || $isSec) ? 'bold' : 'normal';
        $nameFull = $areas[$l]['nombre'];
      @endphp
      <tr>
        {{-- Col 1: indicador de color + nombre completo --}}
        <td style="padding:{{ $rowPad }} 6px {{ $rowPad }} 0;vertical-align:middle;">
          <table style="width:100%;border-collapse:collapse;">
            <tr>
              <td style="width:12px;vertical-align:middle;">
                <div style="width:12px;height:12px;background:{{ $colBar }};border-radius:2px;"></div>
              </td>
              <td style="padding-left:6px;vertical-align:middle;">
                <div style="font-size:11px;font-weight:{{ $fw }};color:{{ $lblColor }};">{{ $nameFull }}</div>
              </td>
            </tr>
          </table>
        </td>
        {{-- Col 2: barra --}}
        <td style="padding:{{ $rowPad }} 3px {{ $rowPad }} 0;vertical-align:middle;">
          <div style="height:{{ $bH }}px;background:#f1f5f9;border-radius:3px;">
            <div style="height:{{ $bH }}px;background:{{ $colBar }};opacity:{{ $opBar }};border-radius:3px;width:{{ $pct }}%;"></div>
          </div>
        </td>
        {{-- Col 3: porcentaje únicamente --}}
        <td style="padding:{{ $rowPad }} 0;text-align:right;vertical-align:middle;">
          <span style="font-size:11px;font-weight:{{ $fw }};color:{{ $pctColor }};">{{ $pct }}%</span>
        </td>
      </tr>
    @endforeach
  </table>
</div>
<div style="height:1px;background:#e2e8f0;"></div>

{{-- ══ INTERESES Y APTITUDES ════════════════════════════════════════════ --}}
<div style="padding:10px {{ $PAD }}px 10px;page-break-inside:avoid;">
  <table style="border-collapse:collapse;margin-bottom:8px;">
    <tr>
      <td style="width:3px;background:#c9a14a;padding:0;"></td>
      <td style="padding-left:8px;">
        <div style="font-size:12px;font-weight:bold;color:#0f172a;text-transform:uppercase;letter-spacing:1px;">Intereses y Aptitudes</div>
      </td>
    </tr>
  </table>

  <table style="width:100%;border-collapse:collapse;">
    <tr>
      {{-- INTERESES --}}
      <td style="width:50%;vertical-align:top;padding-right:7px;">
        <div style="border-top:3px solid {{ $color }};border-left:1px solid #e2e8f0;border-right:1px solid #e2e8f0;border-bottom:1px solid #e2e8f0;padding:10px 12px;">
          <div style="font-size:11px;font-weight:bold;text-transform:uppercase;letter-spacing:1.5px;color:{{ $color }};margin-bottom:8px;">Intereses</div>
          @foreach($areaP['intereses'] as $it)
            <table style="width:100%;border-collapse:collapse;margin-bottom:5px;">
              <tr>
                <td style="width:14px;vertical-align:middle;">
                  <div style="width:14px;height:14px;background:{{ $color }};border-radius:2px;display:table;">
                    <div style="display:table-cell;text-align:center;vertical-align:middle;font-size:9px;color:#fff;font-weight:bold;line-height:1;">&#10003;</div>
                  </div>
                </td>
                <td style="vertical-align:middle;padding-left:6px;">
                  <div style="font-size:11px;color:#1e293b;line-height:1.4;">{{ $it }}</div>
                </td>
              </tr>
            </table>
          @endforeach
        </div>
      </td>
      {{-- APTITUDES --}}
      <td style="width:50%;vertical-align:top;padding-left:7px;">
        <div style="border-top:3px solid #c9a14a;border-left:1px solid #e2e8f0;border-right:1px solid #e2e8f0;border-bottom:1px solid #e2e8f0;padding:10px 12px;">
          <div style="font-size:11px;font-weight:bold;text-transform:uppercase;letter-spacing:1.5px;color:#c9a14a;margin-bottom:8px;">Aptitudes</div>
          @foreach($areaP['aptitudes'] as $it)
            <table style="width:100%;border-collapse:collapse;margin-bottom:5px;">
              <tr>
                <td style="width:14px;vertical-align:middle;">
                  <div style="width:14px;height:14px;background:#c9a14a;border-radius:2px;display:table;">
                    <div style="display:table-cell;text-align:center;vertical-align:middle;font-size:9px;color:#fff;font-weight:bold;line-height:1;">&#10003;</div>
                  </div>
                </td>
                <td style="vertical-align:middle;padding-left:6px;">
                  <div style="font-size:11px;color:#1e293b;line-height:1.4;">{{ $it }}</div>
                </td>
              </tr>
            </table>
          @endforeach
        </div>
      </td>
    </tr>
  </table>
</div>
<div style="height:1px;background:#e2e8f0;"></div>

{{-- ══ CARRERAS RECOMENDADAS ════════════════════════════════════════════ --}}
<div style="padding:10px {{ $PAD }}px 12px;page-break-inside:avoid;">
  <table style="border-collapse:collapse;margin-bottom:8px;">
    <tr>
      <td style="width:3px;background:#c9a14a;padding:0;"></td>
      <td style="padding-left:8px;">
        <div style="font-size:12px;font-weight:bold;color:#0f172a;text-transform:uppercase;letter-spacing:1px;">Carreras Recomendadas</div>
      </td>
    </tr>
  </table>

  <table style="width:100%;border-collapse:collapse;">
    <tr>
      <td style="width:50%;vertical-align:top;padding-right:6px;">
        @foreach(array_slice($areaP['carreras'],0,$half) as $i=>$c)
          <table style="width:100%;border-collapse:collapse;margin-bottom:5px;">
            <tr>
              <td style="border-left:4px solid #c9a14a;border-top:1px solid #e2e8f0;border-right:1px solid #e2e8f0;border-bottom:1px solid #e2e8f0;padding:5px 10px;vertical-align:middle;">
                <span style="font-size:11px;font-weight:bold;color:#c9a14a;">{{ str_pad($i+1,2,'0',STR_PAD_LEFT) }}</span>
                <span style="font-size:11px;color:#1e293b;margin-left:8px;">{{ $c }}</span>
              </td>
            </tr>
          </table>
        @endforeach
      </td>
      <td style="width:50%;vertical-align:top;padding-left:6px;">
        @foreach(array_slice($areaP['carreras'],$half) as $i=>$c)
          <table style="width:100%;border-collapse:collapse;margin-bottom:5px;">
            <tr>
              <td style="border-left:4px solid #c9a14a;border-top:1px solid #e2e8f0;border-right:1px solid #e2e8f0;border-bottom:1px solid #e2e8f0;padding:5px 10px;vertical-align:middle;">
                <span style="font-size:11px;font-weight:bold;color:#c9a14a;">{{ str_pad($i+$half+1,2,'0',STR_PAD_LEFT) }}</span>
                <span style="font-size:11px;color:#1e293b;margin-left:8px;">{{ $c }}</span>
              </td>
            </tr>
          </table>
        @endforeach
      </td>
    </tr>
  </table>
  <div style="margin-top:9px;padding-top:8px;border-top:1px solid #f1f5f9;">
    <div style="font-size:11px;font-weight:bold;color:{{ $color }};letter-spacing:.3px;margin-bottom:4px;">&#9733;&nbsp;Tu mayor fortaleza</div>
    <div style="font-size:11px;color:#334155;line-height:1.65;">{{ $frase }}</div>
  </div>
</div>

</div>
</body>
</html>
