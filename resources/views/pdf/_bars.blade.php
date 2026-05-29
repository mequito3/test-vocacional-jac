{{-- Grafico de barras estatico para dompdf. Params: $ordenAreas,$puntajes,$maximo,$areas,$principal,$secundaria,$barH(px) --}}
@php $barH = $barH ?? 150; @endphp
<table style="width:100%; border-collapse:collapse;">
    <tr>
        @foreach ($ordenAreas as $l)
            @php
                $top = ($l === $principal || $l === $secundaria);
                $h = max(5, round(($puntajes[$l] / $maximo) * $barH));
            @endphp
            <td style="text-align:center; vertical-align:bottom; width:14.28%;">
                <div style="font-size:12px; font-weight:bold; color:#16233f; margin-bottom:3px;">{{ $puntajes[$l] }}</div>
                <div style="width:30px; margin:0 auto; height:{{ $h }}px; background:{{ $areas[$l]['color'] }}; {{ $top ? '' : 'opacity:.4;' }} border-radius:5px 5px 0 0;"></div>
            </td>
        @endforeach
    </tr>
    <tr>
        @foreach ($ordenAreas as $l)
            <td style="text-align:center; padding-top:5px; border-top:2px solid #d1d5db;">
                <span style="font-size:14px; font-weight:bold; color:#16233f;">{{ $l }}</span>
            </td>
        @endforeach
    </tr>
</table>
