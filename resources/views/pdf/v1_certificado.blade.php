<!DOCTYPE html>
<html lang="es"><head><meta charset="utf-8">
@php $areaP=$areas[$principal]; $areaS=$areas[$secundaria]; $logo=public_path('images/logo-jac.png'); $hasLogo=file_exists($logo); @endphp
<style>
    @page { margin:0; }
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family:'DejaVu Sans', sans-serif; color:#2b3242; }
    .frame { margin:16px; border:3px solid #1b3a6b; padding:3px; }
    .frame-in { border:1px solid #c9a14a; padding:26px 34px; }
    .center { text-align:center; }
    .logo { width:74px; height:74px; }
    .inst { font-size:11px; letter-spacing:2px; text-transform:uppercase; color:#1b3a6b; font-weight:bold; margin-top:6px; }
    .rule { width:120px; height:2px; background:#c9a14a; margin:10px auto 0; }
    .kicker { font-size:10px; letter-spacing:3px; text-transform:uppercase; color:#9ca3af; margin-top:18px; }
    .h1 { font-family:'DejaVu Serif', serif; font-size:30px; color:#1b3a6b; margin-top:6px; }
    .otorga { font-size:11px; color:#6b7280; margin-top:18px; }
    .name { font-family:'DejaVu Serif', serif; font-style:italic; font-size:26px; color:#16233f; margin-top:4px; border-bottom:1px solid #e5e7eb; display:inline-block; padding:0 24px 6px; }
    .lead { font-size:12px; color:#4b5563; margin-top:16px; line-height:1.6; }
    .lead b { color:#1b3a6b; }
    .dom-area { font-family:'DejaVu Serif', serif; font-size:18px; color:{{ $areaP['color'] }}; font-weight:bold; margin-top:4px; }
    .chart-wrap { width:70%; margin:18px auto 0; }
    .cols { width:100%; border-collapse:collapse; margin-top:20px; }
    .cols td { width:33.33%; vertical-align:top; padding:0 10px; }
    .ct { font-size:10px; letter-spacing:1.5px; text-transform:uppercase; color:#9c3a1f; font-weight:bold; padding-bottom:5px; border-bottom:1px solid #1b3a6b; margin-bottom:6px; }
    .li { font-size:10px; padding:2px 0; color:#374151; }
    .li .n { color:#c9a14a; font-weight:bold; }
    .foot { margin-top:24px; }
    .foot td { vertical-align:bottom; }
    .sign { border-top:1px solid #9ca3af; width:200px; font-size:9px; color:#9ca3af; padding-top:4px; text-align:center; }
    .seal { text-align:right; width:120px; }
    .seal img { width:84px; height:84px; }
    .rm { font-size:8.5px; color:#9ca3af; margin-top:6px; }
</style></head>
<body>
<div class="frame"><div class="frame-in">
    <div class="center">
        @if($hasLogo)<img class="logo" src="{{ $logo }}">@endif
        <div class="inst">Instituto Técnico Superior JAC Boliviano 2000</div>
        <div class="rule"></div>
        <div class="kicker">Test de Orientación Vocacional · Método CHASIDE</div>
        <div class="h1">Informe de Resultados</div>
        <div class="otorga">Otorgado a</div>
        <div class="name">{{ $estudiante->nombre_completo }}</div>
        <div class="lead">
            Tras completar las 98 preguntas del test, tu perfil revela una vocación dominante por el área de<br>
            <span class="dom-area">{{ $areaP['nombre'] }}</span><br>
            <span style="font-size:11px; color:#6b7280;">con inclinación secundaria por {{ $areaS['nombre'] }}.</span>
        </div>
    </div>

    <div class="chart-wrap">@include('pdf._bars', ['barH' => 120])</div>

    <table class="cols">
        <tr>
            <td>
                <div class="ct">Intereses</div>
                @foreach ($areaP['intereses'] as $i => $it)<div class="li"><span class="n">{{ $i+1 }}.</span> {{ $it }}</div>@endforeach
            </td>
            <td>
                <div class="ct">Aptitudes</div>
                @foreach ($areaP['aptitudes'] as $i => $it)<div class="li"><span class="n">{{ $i+1 }}.</span> {{ $it }}</div>@endforeach
            </td>
            <td>
                <div class="ct">Carreras sugeridas</div>
                @foreach ($areaP['carreras'] as $i => $c)<div class="li"><span class="n">{{ $i+1 }}.</span> {{ $c }}</div>@endforeach
            </td>
        </tr>
    </table>

    <table class="foot"><tr>
        <td><div class="sign">Departamento de Orientación · JAC Boliviano 2000</div><div class="rm">RM. 2450/17 · Tel. 4553737 · 71443907 · {{ $resultado->created_at->format('d/m/Y') }}</div></td>
        <td class="seal">@if($hasLogo)<img src="{{ $logo }}">@endif</td>
    </tr></table>
</div></div>
</body></html>
