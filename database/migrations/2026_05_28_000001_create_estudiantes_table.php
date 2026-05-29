<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabla de estudiantes que rinden el test vocacional CHASIDE.
     */
    public function up(): void
    {
        Schema::create('estudiantes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('apellido', 100);
            $table->enum('sexo', ['Masculino', 'Femenino', 'Otro']);
            $table->unsignedTinyInteger('edad');
            $table->string('celular', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('nombre_madre', 100)->nullable();
            $table->string('celular_madre', 20)->nullable();
            $table->string('nombre_padre', 100)->nullable();
            $table->string('celular_padre', 20)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estudiantes');
    }
};
