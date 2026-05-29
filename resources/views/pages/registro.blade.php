@extends('layouts.app')

@section('title', 'Tu ficha')

@php
  $inputCls = 'w-full rounded-2xl border border-slate-200 bg-white/80 px-4 py-3.5 text-ink placeholder:text-slate-400 focus:border-navy-500 focus:ring-4 focus:ring-navy-500/12 outline-none transition';
@endphp

@section('content')
<div class="mx-auto max-w-3xl px-4 sm:px-6 pt-10 pb-4">

  {{-- Stepper --}}
  <div class="rise d1 flex items-center justify-center gap-2 sm:gap-3 mb-7">
    @php $steps=[['1','Ficha',true],['2','Test',false],['3','Resultado',false]]; @endphp
    @foreach ($steps as $i => [$n,$lbl,$active])
      <div class="flex items-center gap-2">
        <span class="grid place-items-center w-8 h-8 rounded-full text-sm font-bold {{ $active ? 'bg-navy-700 text-white shadow-btn' : 'bg-white text-slate-400 border border-slate-200' }}">{{ $n }}</span>
        <span class="text-sm font-semibold {{ $active ? 'text-ink' : 'text-slate-400' }} hidden sm:inline">{{ $lbl }}</span>
      </div>
      @if (!$loop->last)<span class="w-6 sm:w-10 h-0.5 rounded bg-slate-200"></span>@endif
    @endforeach
  </div>

  <div class="rise d2 text-center mb-7">
    <h1 class="font-display font-extrabold text-ink text-4xl sm:text-5xl tracking-tight">Cuéntanos sobre ti</h1>
    <p class="text-slate-500 mt-2">Tus datos quedan solo con tu informe. Los campos con <span class="text-rose-500 font-semibold">*</span> son obligatorios.</p>
  </div>

  @if ($errors->any())
    <div class="rise d2 mb-6 rounded-2xl border border-rose-200 bg-rose-50/80 px-5 py-4 text-sm text-rose-700">
      <p class="font-semibold mb-1 flex items-center gap-2">
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9 9a1 1 0 012 0v4a1 1 0 11-2 0V9zm1-5a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"/></svg>
        Revisa estos campos:
      </p>
      <ul class="list-disc list-inside space-y-0.5 ml-1">
        @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('registro.guardar') }}" class="space-y-5">
    @csrf

    {{-- Datos del estudiante --}}
    <div class="rise d3 card rounded-3xl shadow-card p-6 sm:p-7">
      <div class="flex items-center gap-3 mb-5">
        <span class="grid place-items-center w-10 h-10 rounded-xl bg-navy-700/10 text-navy-700">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        </span>
        <h2 class="font-display font-bold text-ink text-lg">Datos del estudiante</h2>
      </div>
      <div class="grid sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-semibold text-slate-600 mb-1.5">Nombre <span class="text-rose-500">*</span></label>
          <input name="nombre" value="{{ old('nombre') }}" required placeholder="Erika" class="{{ $inputCls }} @error('nombre') border-rose-400 @enderror">
        </div>
        <div>
          <label class="block text-sm font-semibold text-slate-600 mb-1.5">Apellido <span class="text-rose-500">*</span></label>
          <input name="apellido" value="{{ old('apellido') }}" required placeholder="Pérez Calvimontes" class="{{ $inputCls }} @error('apellido') border-rose-400 @enderror">
        </div>
        <div>
          <label class="block text-sm font-semibold text-slate-600 mb-1.5">Sexo <span class="text-rose-500">*</span></label>
          <select name="sexo" required class="{{ $inputCls }} @error('sexo') border-rose-400 @enderror">
            <option value="">Indicar…</option>
            @foreach (['Femenino','Masculino','Otro'] as $op)
              <option value="{{ $op }}" @selected(old('sexo') === $op)>{{ $op }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-sm font-semibold text-slate-600 mb-1.5">Edad <span class="text-rose-500">*</span></label>
          <input type="number" name="edad" value="{{ old('edad') }}" min="12" max="35" required placeholder="18" class="{{ $inputCls }} @error('edad') border-rose-400 @enderror">
        </div>
        <div>
          <label class="block text-sm font-semibold text-slate-600 mb-1.5">Celular</label>
          <input name="celular" value="{{ old('celular') }}" placeholder="+591 7XXXXXXX" class="{{ $inputCls }}">
        </div>
        <div>
          <label class="block text-sm font-semibold text-slate-600 mb-1.5">Correo electrónico</label>
          <input type="email" name="email" value="{{ old('email') }}" placeholder="nombre@correo.com" class="{{ $inputCls }} @error('email') border-rose-400 @enderror">
        </div>
      </div>
    </div>

    {{-- Datos de los padres --}}
    <div class="rise d4 card rounded-3xl shadow-card p-6 sm:p-7">
      <div class="flex items-center gap-3 mb-5">
        <span class="grid place-items-center w-10 h-10 rounded-xl bg-gold-500/15 text-gold-600">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-1a4 4 0 00-3-3.87M9 20H4v-1a4 4 0 013-3.87m6-1.13a4 4 0 10-4 0M19 8a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </span>
        <h2 class="font-display font-bold text-ink text-lg">Datos de los padres <span class="text-sm font-normal text-slate-400">(opcional)</span></h2>
      </div>
      <div class="grid sm:grid-cols-2 gap-4">
        <div><label class="block text-sm font-semibold text-slate-600 mb-1.5">Nombre de la madre</label><input name="nombre_madre" value="{{ old('nombre_madre') }}" placeholder="Nombre completo" class="{{ $inputCls }}"></div>
        <div><label class="block text-sm font-semibold text-slate-600 mb-1.5">Celular de la madre</label><input name="celular_madre" value="{{ old('celular_madre') }}" placeholder="+591 7XXXXXXX" class="{{ $inputCls }}"></div>
        <div><label class="block text-sm font-semibold text-slate-600 mb-1.5">Nombre del padre</label><input name="nombre_padre" value="{{ old('nombre_padre') }}" placeholder="Nombre completo" class="{{ $inputCls }}"></div>
        <div><label class="block text-sm font-semibold text-slate-600 mb-1.5">Celular del padre</label><input name="celular_padre" value="{{ old('celular_padre') }}" placeholder="+591 7XXXXXXX" class="{{ $inputCls }}"></div>
      </div>
    </div>

    <div class="rise d5 flex items-center justify-between gap-4 pt-1">
      <a href="{{ route('welcome') }}" class="text-slate-500 hover:text-ink font-semibold text-sm">← Volver</a>
      <button type="submit" class="group inline-flex items-center gap-2.5 rounded-2xl bg-navy-700 hover:bg-navy-600 text-white font-semibold px-8 py-4 shadow-btn hover:-translate-y-0.5 transition-all">
        Comenzar el test
        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
      </button>
    </div>
  </form>
</div>
@endsection
