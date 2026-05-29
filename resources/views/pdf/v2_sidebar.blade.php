<!DOCTYPE html>
<html lang="es"><head><meta charset="utf-8">
@php $areaP=$areas[$principal]; $areaS=$areas[$secundaria]; $logo=public_path('images/logo-jac.png'); $hasLogo=file_exists($logo); @endphp
<style>
    @page { margin:0; }
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family:'DejaVu Sans', sans-serif; color:#2b3242; font-size:11px; margin-left:228px; }
    /* Barra lateral fija a toda la altura de la pagina */
    .side { position:fixed; top:0; left:0; width:200px; height:1123px; background:#1b3a6b; color:#fff; padding:24px 18px; }
    .side .logo { width:64px; height:64px; display:block; margin:0 auto 10px; background:#fff; border-radius:50%; }
    .side .inst { font-size:10px; text-align:center; color:#cdd9ef; line-height:1.4; padding-bottom:14px; border-bottom:1px solid rgba(255,255,255,.2); }
    .side .lbl { font-size:8px; letter-spacing:1.5px; text-transform:uppercase; color:#f5a623; margin-top:16px; }
    .side .val { font-size:12px; margin-top:2px; }
    .side .dom { margin-top:20px; background:rgba(255,255,255,.1); border-radius:8px; padding:12px; }
    .side .dom .a { font-size:14px; font-weight:bold; margin-top:3px; }
    .main { padding:26px 28px; }
    .fel { font-size:22px; color:#1b3a6b; font-weight:bold; }
    .fel span { color:#f5a623; }
    .sub { font-size:9.5px; color:#9ca3af; text-transform:uppercase; letter-spacing:1px; margin:2px 0 18px; }
    .sech { font-size:11px; font-weight:bold; color:#1b3a6b; text-transform:uppercase; letter-spacing:.5px; margin:18px 0 8px; }
    .sech.first { margin-top:0; }
    .cols { width:100%; border-collapse:collapse; }
    .cols td { width:50%; vertical-align:top; padding-right:12px; }
    .tag { display:inline-block; background:#eef3fb; color:#1b3a6b; font-size:9.5px; padding:3px 8px; border-radius:10px; margin:0 3px 4px 0; }
    .career { font-size:10.5px; padding:3px 0; color:#374151; }
    .career .n { color:#f5a623; font-weight:bold; margin-right:5px; }
</style></head>
<body>
<div class="side">
    @if($hasLogo)<img class="logo" src="{{ $logo }}">@endif
    <div class="inst">Instituto Técnico Superior<br>JAC Boliviano 2000</div>
    <div class="lbl">Estudiante</div>
    <div class="val">{{ $estudiante->nombre_completo }}</div>
    <div class="lbl">Fecha</div>
    <div class="val">{{ $resultado->created_at->format('d/m/Y') }}</div>
    <div class="lbl">Test</div>
    <div class="val">CHASIDE · 98 preguntas</div>
    <div class="dom">
        <div class="lbl" style="margin-top:0;">Área dominante</div>
        <div class="a">{{ $areaP['nombre'] }}</div>
        <div style="font-size:9px; color:#cdd9ef; margin-top:4px;">2ª: {{ $areaS['nombre'] }}</div>
    </div>
</div>

<div class="main">
    <div class="fel"><span>¡Felicidades!</span> {{ $estudiante->nombre }}</div>
    <div class="sub">Informe de Orientación Vocacional</div>

    <div class="sech first">Puntaje por área</div>
    @include('pdf._bars', ['barH' => 130])

    <table class="cols" style="margin-top:18px;">
        <tr>
            <td>
                <div class="sech" style="margin:0 0 8px;">Tus intereses</div>
                @foreach ($areaP['intereses'] as $it)<span class="tag">{{ $it }}</span>@endforeach
            </td>
            <td>
                <div class="sech" style="margin:0 0 8px;">Tus aptitudes</div>
                @foreach ($areaP['aptitudes'] as $it)<span class="tag">{{ $it }}</span>@endforeach
            </td>
        </tr>
    </table>

    <div class="sech">Carreras sugeridas</div>
    <table style="width:100%;"><tr>
        <td style="width:50%; vertical-align:top;">
            @foreach (array_slice($areaP['carreras'],0,ceil(count($areaP['carreras'])/2)) as $i => $c)<div class="career"><span class="n">{{ str_pad($i+1,2,'0',STR_PAD_LEFT) }}</span>{{ $c }}</div>@endforeach
        </td>
        <td style="width:50%; vertical-align:top;">
            @foreach (array_slice($areaP['carreras'],ceil(count($areaP['carreras'])/2)) as $i => $c)<div class="career"><span class="n">{{ str_pad($i+1+ceil(count($areaP['carreras'])/2),2,'0',STR_PAD_LEFT) }}</span>{{ $c }}</div>@endforeach
        </td>
    </tr></table>

    <div style="margin-top:24px; font-size:8.5px; color:#9ca3af; border-top:1px solid #e5e7eb; padding-top:8px;">
        Departamento de Orientación · Tel. 4553737 · 71443907 · 61624258 · RM. 2450/17
    </div>
</div>
</body></html>
