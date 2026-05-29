<!DOCTYPE html>
<html lang="es"><head><meta charset="utf-8">
@php $areaP=$areas[$principal]; $areaS=$areas[$secundaria]; $logo=public_path('images/logo-jac.png'); $hasLogo=file_exists($logo); @endphp
<style>
    @page { margin:0; }
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family:'DejaVu Sans', sans-serif; color:#374151; font-size:11px; }

    /* Barra superior */
    .topbar { background:#16233f; color:#fff; padding:16px 34px; }
    .topbar table { width:100%; }
    .topbar .logo { width:48px; }
    .topbar .logo img { width:44px; height:44px; background:#fff; border-radius:50%; }
    .topbar .t { padding-left:12px; vertical-align:middle; }
    .topbar .t .a { font-size:13px; font-weight:bold; letter-spacing:.5px; }
    .topbar .t .b { font-size:8.5px; color:#aab6cf; letter-spacing:2px; text-transform:uppercase; }
    .topbar .r { text-align:right; vertical-align:middle; font-size:8.5px; color:#aab6cf; letter-spacing:1.5px; text-transform:uppercase; }
    .goldbar { height:3px; background:#c9a14a; }

    .body { padding:24px 34px; }

    /* Bloque del estudiante */
    .head { width:100%; border-collapse:collapse; border-bottom:2px solid #eef0f4; padding-bottom:14px; }
    .head .name { font-size:24px; color:#16233f; font-weight:bold; }
    .head .role { font-size:10px; color:#9ca3af; margin-top:2px; }
    .head .badge { text-align:right; vertical-align:middle; }
    .head .badge .tag { display:inline-block; color:#fff; font-size:11px; font-weight:bold; padding:7px 14px; border-radius:4px; background:{{ $areaP['color'] }}; }
    .head .badge .tl { font-size:8px; letter-spacing:1.5px; text-transform:uppercase; color:#9ca3af; margin-bottom:4px; }

    /* Titulos de seccion */
    .sec { margin-top:22px; }
    .sec .h { font-size:11px; font-weight:bold; color:#16233f; text-transform:uppercase; letter-spacing:1px; padding-left:9px; border-left:3px solid #c9a14a; margin-bottom:12px; }

    /* Panel */
    .panel { border:1px solid #e5e7eb; border-radius:8px; padding:18px 16px 10px; }

    /* Intereses / aptitudes */
    .cols { width:100%; border-collapse:collapse; }
    .cols > td { width:50%; vertical-align:top; padding-right:14px; }
    .box { border:1px solid #e5e7eb; border-radius:8px; padding:14px 16px; }
    .box .bt { font-size:10px; font-weight:bold; text-transform:uppercase; letter-spacing:.8px; color:#16233f; margin-bottom:8px; padding-bottom:6px; border-bottom:1px solid #eef0f4; }
    .item { font-size:10.5px; padding:3px 0; color:#374151; }
    .item .ck { color:{{ $areaP['color'] }}; font-weight:bold; margin-right:6px; }

    /* Carreras */
    .career { font-size:11px; color:#16233f; padding:6px 0; }
    .career .n { display:inline-block; width:22px; height:22px; line-height:22px; text-align:center; background:#16233f; color:#fff; border-radius:4px; font-size:9px; font-weight:bold; margin-right:8px; }

    /* Pie */
    .footbar { background:#16233f; color:#aab6cf; font-size:8.5px; letter-spacing:.5px; padding:10px 34px; text-align:center; }
</style></head>
<body>
<div class="topbar">
    <table><tr>
        <td class="logo">@if($hasLogo)<img src="{{ $logo }}">@endif</td>
        <td class="t"><div class="a">Instituto Técnico Superior JAC Bolivia 2000</div><div class="b">Orientación Vocacional · Método CHASIDE</div></td>
        <td class="r">Informe de Resultados<br>{{ $resultado->created_at->format('d/m/Y') }}</td>
    </tr></table>
</div>
<div class="goldbar"></div>

<div class="body">
    <table class="head"><tr>
        <td>
            <div class="name">{{ $estudiante->nombre_completo }}</div>
            <div class="role">Estudiante · Test CHASIDE de 98 preguntas</div>
        </td>
        <td class="badge">
            <div class="tl">Área vocacional dominante</div>
            <div class="tag">{{ $areaP['nombre'] }}</div>
        </td>
    </tr></table>

    <div class="sec">
        <div class="h">Resultados por área</div>
        <div class="panel">@include('pdf._bars', ['barH' => 135])</div>
    </div>

    <div class="sec">
        <div class="h">Perfil: intereses y aptitudes</div>
        <table class="cols"><tr>
            <td>
                <div class="box">
                    <div class="bt">Tus intereses</div>
                    @foreach ($areaP['intereses'] as $it)<div class="item"><span class="ck">✓</span>{{ $it }}</div>@endforeach
                </div>
            </td>
            <td>
                <div class="box">
                    <div class="bt">Tus aptitudes</div>
                    @foreach ($areaP['aptitudes'] as $it)<div class="item"><span class="ck">✓</span>{{ $it }}</div>@endforeach
                </div>
            </td>
        </tr></table>
    </div>

    <div class="sec">
        <div class="h">Carreras recomendadas</div>
        <table style="width:100%;"><tr>
            <td style="width:50%; vertical-align:top; padding-right:18px;">
                @foreach (array_slice($areaP['carreras'],0,ceil(count($areaP['carreras'])/2)) as $i => $c)<div class="career"><span class="n">{{ str_pad($i+1,2,'0',STR_PAD_LEFT) }}</span>{{ $c }}</div>@endforeach
            </td>
            <td style="width:50%; vertical-align:top;">
                @foreach (array_slice($areaP['carreras'],ceil(count($areaP['carreras'])/2)) as $i => $c)<div class="career"><span class="n">{{ str_pad($i+1+ceil(count($areaP['carreras'])/2),2,'0',STR_PAD_LEFT) }}</span>{{ $c }}</div>@endforeach
            </td>
        </tr></table>
    </div>

    <div style="margin-top:18px; text-align:right; color:#cbd2dd; font-size:9px;">
        @if($hasLogo)<img src="{{ $logo }}" style="width:54px; height:54px; opacity:.9;">@endif
    </div>
</div>

<div class="footbar">
    INSTITUTO TÉCNICO SUPERIOR JAC BOLIVIA 2000 · Av. San Martín esq. Brasil, Ed. Pruber 901, 2do Piso · Cochabamba — Bolivia · Tel. 4553737 · 71443907 · 61624258 · RM. 2450/17
</div>
</body></html>
