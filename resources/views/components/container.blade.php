{{-- Contenedor centrado reutilizable. Uso: <x-container>, <x-container size="form">, etc. --}}
@props(['size' => 'default'])
@php
    $max = match ($size) {
        'narrow' => 'max-w-2xl',   // test (1 pregunta)
        'form'   => 'max-w-3xl',   // formularios
        'wide'   => 'max-w-6xl',
        default  => 'max-w-5xl',   // header, footer, welcome, resultado
    };
@endphp
<div {{ $attributes->merge(['class' => "mx-auto $max px-5 sm:px-6"]) }}>
    {{ $slot }}
</div>
