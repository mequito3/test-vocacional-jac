<!DOCTYPE html>
<html lang="es"><head><meta charset="utf-8">
@php $areaP=$areas[$principal]; $areaS=$areas[$secundaria]; $logo=public_path('images/logo-jac.png'); $hasLogo=file_exists($logo); @endphp
<style>
    @page { margin:0; }
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family:'DejaVu Sans', sans-serif; color:#374151; font-size:11px; }
    .wrap { padding:40px 46px; }
    .top { width:100%; border-collapse:collapse; padding-bottom:16px; border-bottom:1px solid #e5e7eb; }
    .top .logo { width:46px; }
    .top .logo img { width:42px; height:42px; }
    .top .inst { font-size:12px; font-weight:bold; color:#1b3a6b; padding-left:10px; }
    .top .inst span { display:block; font-size:8.5px; font-weight:normal; color:#9ca3af; letter-spacing:1px; text-transform:uppercase; margin-top:2px; }
    .top .meta { text-align:right; font-size:9px; color:#9ca3af; }
    .name { font-size:26px; color:#16233f; font-weight:bold; margin-top:26px; }
    .dom { font-size:12px; color:#6b7280; margin-top:6px; }
    .dom b { color:{{ $areaP['color'] }}; font-size:13px; }
    .label { font-size:9px; letter-spacing:2px; text-transform:uppercase; color:#9ca3af; margin:34px 0 14px; }
    .cols { width:100%; border-collapse:collapse; margin-top:6px; }
    .cols td { width:50%; vertical-align:top; padding-right:24px; }
    .pair { padding:5px 0; border-bottom:1px solid #f1f3f7; font-size:11px; }
    .pair .n { color:#9ca3af; width:20px; display:inline-block; }
    .careers { margin-top:8px; }
    .careers .c { font-size:12px; color:#1b3a6b; padding:6px 0; border-bottom:1px solid #f1f3f7; }
    .careers .c .dot { display:inline-block; width:6px; height:6px; border-radius:50%; background:{{ $areaP['color'] }}; margin-right:8px; vertical-align:middle; }
    .foot { margin-top:40px; padding-top:12px; border-top:1px solid #e5e7eb; font-size:8.5px; color:#b0b7c3; letter-spacing:.5px; }
</style></head>
<body><div class="wrap">
    <table class="top"><tr>
        <td class="logo">@if($hasLogo)<img src="{{ $logo }}">@endif</td>
        <td class="inst">JAC Boliviano 2000<span>Orientación Vocacional · CHASIDE</span></td>
        <td class="meta">Informe de resultados<br>{{ $resultado->created_at->format('d/m/Y') }}</td>
    </tr></table>

    <div class="name">{{ $estudiante->nombre_completo }}</div>
    <div class="dom">Tu área vocacional dominante es <b>{{ $areaP['nombre'] }}</b>, con inclinación secundaria por {{ $areaS['nombre'] }}.</div>

    <div class="label">Puntaje por área</div>
    @include('pdf._bars', ['barH' => 150])

    <table class="cols">
        <tr>
            <td>
                <div class="label" style="margin:34px 0 10px;">Intereses</div>
                @foreach ($areaP['intereses'] as $i => $it)<div class="pair"><span class="n">{{ $i+1 }}</span>{{ $it }}</div>@endforeach
            </td>
            <td>
                <div class="label" style="margin:34px 0 10px;">Aptitudes</div>
                @foreach ($areaP['aptitudes'] as $i => $it)<div class="pair"><span class="n">{{ $i+1 }}</span>{{ $it }}</div>@endforeach
            </td>
        </tr>
    </table>

    <div class="label">Carreras sugeridas</div>
    <div class="careers">
        <table style="width:100%;"><tr>
            <td style="width:50%; vertical-align:top; padding-right:24px;">
                @foreach (array_slice($areaP['carreras'],0,ceil(count($areaP['carreras'])/2)) as $c)<div class="c"><span class="dot"></span>{{ $c }}</div>@endforeach
            </td>
            <td style="width:50%; vertical-align:top;">
                @foreach (array_slice($areaP['carreras'],ceil(count($areaP['carreras'])/2)) as $c)<div class="c"><span class="dot"></span>{{ $c }}</div>@endforeach
            </td>
        </tr></table>
    </div>

    <div class="foot">INSTITUTO TÉCNICO SUPERIOR JAC BOLIVIANO 2000 · AV. SAN MARTÍN ESQ. BRASIL, ED. PRUBER 901 · COCHABAMBA — BOLIVIA · TEL. 4553737 · 71443907 · 61624258 · RM. 2450/17</div>
</div></body></html>
