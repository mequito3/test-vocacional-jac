<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('resultados_chaside', function (Blueprint $table) {
            $table->string('share_token', 32)->nullable()->unique()->after('respuestas_json');
        });
    }

    public function down(): void
    {
        Schema::table('resultados_chaside', function (Blueprint $table) {
            $table->dropColumn('share_token');
        });
    }
};
