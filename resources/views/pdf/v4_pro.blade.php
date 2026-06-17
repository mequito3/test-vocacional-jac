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

    $hex = ltrim($areaP['color'], '#');
    $r   = hexdec(substr($hex, 0, 2));
    $g   = hexdec(substr($hex, 2, 2));
    $b   = hexdec(substr($hex, 4, 2));
    $colorBg  = sprintf('rgb(%d,%d,%d)', $r+(255-$r)*.92, $g+(255-$g)*.92, $b+(255-$b)*.92);
    $colorBdr = sprintf('rgb(%d,%d,%d)', $r+(255-$r)*.72, $g+(255-$g)*.72, $b+(255-$b)*.72);

    $BAR_H = 96;
    $COL   = 30;           // px ancho de cada barra
    $PAD   = 32;           // padding horizontal de página (px)
    $HDR_H = 64;           // altura header
    $FTR_H = 25;           // altura footer
    $GOLD  = 3;            // altura franja dorada
@endphp
<style>
/* ── PÁGINA ────────────────────── */
@page  { size: A4 portrait; margin: 0; }
*      { margin: 0; padding: 0; box-sizing: border-box; }
body   { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #374151; background: #fff; }

/* ── HEADER FIJO (se repite en cada página) ── */
#hdr {
    position: fixed;
    top: 0; left: 0; right: 0;
    height: {{ $HDR_H }}px;
    background: #16233f;
}
.hdr-tbl { width: 100%; border-collapse: collapse; height: {{ $HDR_H }}px; padding: 0 {{ $PAD }}px; }
.hdr-logo-td { width: 44px; vertical-align: middle; }
.hdr-logo-td img { width: 40px; height: 40px; border-radius: 50%; display: block; }
.hdr-text-td { padding-left: 11px; vertical-align: middle; }
.hdr-name { font-size: 13px; font-weight: bold; color: #fff; }
.hdr-sub  { font-size: 7px; color: #8da3c4; letter-spacing: 2px; text-transform: uppercase; margin-top: 2px; }
.hdr-date { text-align: right; vertical-align: middle; font-size: 7px; color: #8da3c4; letter-spacing: .6px; text-transform: uppercase; line-height: 2; }

/* franja dorada bajo el header */
#goldtop {
    position: fixed;
    top: {{ $HDR_H }}px; left: 0; right: 0;
    height: {{ $GOLD }}px;
    background: #c9a14a;
}

/* ── FOOTER FIJO (se repite en cada página) ── */
#ftr-gold {
    position: fixed;
    bottom: {{ $FTR_H }}px; left: 0; right: 0;
    height: {{ $GOLD }}px;
    background: #c9a14a;
}
#ftr {
    position: fixed;
    bottom: 0; left: 0; right: 0;
    height: {{ $FTR_H }}px;
    background: #16233f;
    text-align: center;
    color: #7a91b5;
    font-size: 6.5px;
    letter-spacing: .5px;
    padding-top: 6px;
    line-height: 1.7;
}

/* ── CONTENIDO: deja espacio al header y footer ── */
#page {
    margin-top: {{ $HDR_H + $GOLD }}px;
    margin-bottom: {{ $FTR_H + $GOLD }}px;
    padding: 0 {{ $PAD }}px;
}

/* ── BLOQUE ESTUDIANTE ─────────── */
.sblk {
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    padding: 12px {{ $PAD }}px;
    margin-left: -{{ $PAD }}px;
    margin-right: -{{ $PAD }}px;
}
.sblk-tbl { width: 100%; border-collapse: collapse; }
.sname    { font-size: 19px; font-weight: bold; color: #0f172a; line-height: 1.15; }
.smeta    { font-size: 7.5px; color: #94a3b8; margin-top: 3px; letter-spacing: .3px; }
.badge-td { text-align: right; vertical-align: middle; white-space: nowrap; }
.badge-lbl{ font-size: 6.5px; text-transform: uppercase; letter-spacing: 1.5px; color: #94a3b8; margin-bottom: 4px; }
.badge    { background: {{ $areaP['color'] }}; color: #fff; font-size: 10px; font-weight: bold; padding: 5px 13px; border-radius: 3px; display: inline-block; }

/* ── LABELS DE SECCIÓN ──────────── */
.sec-lbl {
    font-size: 7.5px; font-weight: bold; text-transform: uppercase;
    letter-spacing: 2px; color: #64748b;
    padding-left: 8px; border-left: 3px solid #c9a14a;
    margin: 15px 0 9px;
}

/* ── TARJETA ÁREA PRINCIPAL ──────── */
.acard     { background: {{ $colorBg }}; border: 1px solid {{ $colorBdr }}; border-radius: 5px; padding: 11px 14px; }
.acard-tbl { width: 100%; border-collapse: collapse; }
.aletra    { width: 50px; height: 50px; background: {{ $areaP['color'] }}; color: #fff; font-size: 22px; font-weight: bold; text-align: center; line-height: 50px; border-radius: 5px; }
.ainfo-td  { padding-left: 12px; vertical-align: middle; }
.albl      { font-size: 6.5px; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; color: {{ $areaP['color'] }}; margin-bottom: 3px; }
.aname     { font-size: 17px; font-weight: bold; color: #0f172a; line-height: 1.2; }
.asec      { font-size: 8px; color: #64748b; margin-top: 3px; }
.anum-td   { width: 68px; text-align: right; vertical-align: middle; border-left: 1px solid {{ $colorBdr }}; padding-left: 13px; }
.anum      { font-size: 37px; font-weight: bold; color: {{ $areaP['color'] }}; line-height: 1; }
.anumlbl   { font-size: 6.5px; text-transform: uppercase; letter-spacing: 1.2px; color: #94a3b8; margin-top: 2px; }

/* ── GRÁFICO DE BARRAS ───────────── */
.chart-wrap { border: 1px solid #e2e8f0; border-radius: 5px; background: #fafafa; padding: 10px 14px 6px; }
.chart-tbl  { width: 100%; border-collapse: collapse; }
.val-td { text-align: center; vertical-align: bottom; padding: 0 2px; }
.lbl-td { text-align: center; border-top: 2px solid #d1d5db; padding-top: 4px; }
.bar-val  { font-size: 10px; font-weight: bold; color: #16233f; margin-bottom: 2px; }
.bar-ltr  { font-size: 12px; font-weight: bold; }
.bar-name { font-size: 6px; color: #94a3b8; margin-top: 2px; }

/* ── INTERESES / APTITUDES ───────── */
.twocol    { width: 100%; border-collapse: collapse; }
.twocol td { width: 50%; vertical-align: top; }
.twocol td:first-child { padding-right: 7px; }
.box       { border: 1px solid #e2e8f0; border-radius: 5px; padding: 9px 11px; }
.boxtitle  { font-size: 7.5px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; color: #0f172a; padding-bottom: 5px; border-bottom: 1px solid #f1f5f9; margin-bottom: 6px; }
.bxitem    { font-size: 9px; color: #374151; padding: 2px 0; }
.ck        { color: {{ $areaP['color'] }}; font-weight: bold; margin-right: 4px; }

/* ── CARRERAS ────────────────────── */
.ctable    { width: 100%; border-collapse: collapse; }
.ctable td { width: 50%; vertical-align: top; }
.ctable td:first-child { padding-right: 12px; }
.citem  { padding: 4px 0; border-bottom: 1px solid #f1f5f9; font-size: 9.5px; color: #1e293b; }
.cnum   { display: inline-block; width: 17px; height: 17px; line-height: 17px; text-align: center; background: {{ $areaP['color'] }}; color: #fff; border-radius: 3px; font-size: 7px; font-weight: bold; margin-right: 6px; }
</style>
</head>
<body>

{{-- ▌HEADER FIJO --}}
<div id="hdr">
    <table class="hdr-tbl"><tr>
        <td class="hdr-logo-td">
            @if($hasLogo)
                <img src="{{ $logo }}">
            @endif
        </td>
        <td class="hdr-text-td">
            <div class="hdr-name">JAC Bolivia 2000</div>
            <div class="hdr-sub">Orientación Vocacional · Método CHASIDE</div>
        </td>
        <td class="hdr-date">
            Informe de Resultados<br>
            {{ $resultado->created_at->format('d/m/Y') }}
        </td>
    </tr></table>
</div>
<div id="goldtop"></div>

{{-- ▌FOOTER FIJO --}}
<div id="ftr-gold"></div>
<div id="ftr">
    JAC BOLIVIA 2000 &nbsp;·&nbsp; Av. San Martín esq. Brasil, Ed. Pruber 901, 2do Piso &nbsp;·&nbsp; Cochabamba — Bolivia<br>
    Tel. 4553737 &nbsp;·&nbsp; 71443907 &nbsp;·&nbsp; 61624258 &nbsp;·&nbsp; RM. 2450/17
</div>

{{-- ▌CONTENIDO --}}
<div id="page">

    {{-- Estudiante --}}
    <div class="sblk">
        <table class="sblk-tbl"><tr>
            <td>
                <div class="sname">{{ $estudiante->nombre_completo }}</div>
                <div class="smeta">
                    {{ $estudiante->sexo }}&nbsp;·&nbsp;{{ $estudiante->edad }} años
                    @if(isset($estudiante->colegio) && $estudiante->colegio)
                        &nbsp;·&nbsp;{{ $estudiante->colegio->nombre }}
                    @endif
                    &nbsp;·&nbsp;Test CHASIDE — 98 preguntas
                </div>
            </td>
            <td class="badge-td">
                <div class="badge-lbl">Área vocacional dominante</div>
                <div class="badge">{{ $areaP['nombre'] }}</div>
            </td>
        </tr></table>
    </div>

    {{-- Perfil vocacional --}}
    <div class="sec-lbl">Perfil vocacional</div>
    <div class="acard">
        <table class="acard-tbl"><tr>
            <td style="width:50px; vertical-align:middle;">
                <div class="aletra">{{ $principal }}</div>
            </td>
            <td class="ainfo-td">
                <div class="albl">Área dominante</div>
                <div class="aname">{{ $areaP['nombre'] }}</div>
                <div class="asec">Área secundaria:&nbsp;<span style="color:{{ $areaS['color'] }};font-weight:bold;">{{ $secundaria }} — {{ $areaS['nombre'] }}</span></div>
            </td>
            <td class="anum-td">
                <div class="anum">{{ $puntajes[$principal] }}</div>
                <div class="anumlbl">de 14 pts</div>
            </td>
        </tr></table>
    </div>

    {{-- Barras por área --}}
    <div class="sec-lbl">Puntajes por área</div>
    <div class="chart-wrap">
        <table class="chart-tbl">
            <tr>
                @foreach ($ordenAreas as $l)
                    @php
                        $isTop = ($l === $principal || $l === $secundaria);
                        $barH  = max(4, (int) round(($puntajes[$l] / 14) * $BAR_H));
                    @endphp
                    <td class="val-td">
                        <div class="bar-val" style="{{ $isTop ? '' : 'color:#94a3b8;font-weight:normal;' }}">{{ $puntajes[$l] }}</div>
                        <div style="width:{{ $COL }}px;margin:0 auto;height:{{ $barH }}px;background:{{ $areas[$l]['color'] }};border-radius:3px 3px 0 0;{{ $isTop ? '' : 'opacity:.28;' }}"></div>
                    </td>
                @endforeach
            </tr>
            <tr>
                @foreach ($ordenAreas as $l)
                    @php $isTop = ($l === $principal || $l === $secundaria); @endphp
                    <td class="lbl-td">
                        <div class="bar-ltr" style="color:{{ $areas[$l]['color'] }};{{ $isTop ? '' : 'opacity:.38;' }}">{{ $l }}</div>
                        <div class="bar-name">{{ $areas[$l]['nombre'] }}</div>
                    </td>
                @endforeach
            </tr>
        </table>
    </div>

    {{-- Intereses y aptitudes --}}
    <div class="sec-lbl">Intereses y aptitudes</div>
    <table class="twocol"><tr>
        <td>
            <div class="box">
                <div class="boxtitle">Tus intereses</div>
                @foreach ($areaP['intereses'] as $it)
                    <div class="bxitem"><span class="ck">&#10003;</span>{{ $it }}</div>
                @endforeach
            </div>
        </td>
        <td>
            <div class="box">
                <div class="boxtitle">Tus aptitudes</div>
                @foreach ($areaP['aptitudes'] as $it)
                    <div class="bxitem"><span class="ck">&#10003;</span>{{ $it }}</div>
                @endforeach
            </div>
        </td>
    </tr></table>

    {{-- Carreras recomendadas --}}
    <div class="sec-lbl">Carreras recomendadas</div>
    <table class="ctable"><tr>
        <td>
            @foreach (array_slice($areaP['carreras'], 0, $half) as $i => $c)
                <div class="citem"><span class="cnum">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>{{ $c }}</div>
            @endforeach
        </td>
        <td>
            @foreach (array_slice($areaP['carreras'], $half) as $i => $c)
                <div class="citem"><span class="cnum">{{ str_pad($i + $half + 1, 2, '0', STR_PAD_LEFT) }}</span>{{ $c }}</div>
            @endforeach
        </td>
    </tr></table>

</div>
</body>
</html>
