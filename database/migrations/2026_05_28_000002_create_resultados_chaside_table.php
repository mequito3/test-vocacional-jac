<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Resultados del test CHASIDE: puntaje por cada una de las 7 areas,
     * area principal/secundaria y el detalle de las 98 respuestas en JSON.
     */
    public function up(): void
    {
        Schema::create('resultados_chaside', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estudiante_id')->constrained('estudiantes')->cascadeOnDelete();
            $table->unsignedTinyInteger('puntaje_c'); // Administrativas y Contables
            $table->unsignedTinyInteger('puntaje_h'); // Humanisticas y Sociales
            $table->unsignedTinyInteger('puntaje_a'); // Artisticas
            $table->unsignedTinyInteger('puntaje_s'); // Medicina y Cs. de la Salud
            $table->unsignedTinyInteger('puntaje_i'); // Ingenieria y Computacion
            $table->unsignedTinyInteger('puntaje_d'); // Defensa y Seguridad
            $table->unsignedTinyInteger('puntaje_e'); // Ciencias Exactas y Agrarias
            $table->char('area_principal', 1);
            $table->char('area_secundaria', 1);
            $table->json('respuestas_json'); // array de 98 booleans
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resultados_chaside');
    }
};
