<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usuario', function (Blueprint $table) {
            // Distingue "admin de la plataforma" (panel web, Rodolfo) de "admin de un círculo"
            $table->boolean('es_admin_plataforma')->default(false)->after('activo');
        });
    }

    public function down(): void
    {
        Schema::table('usuario', function (Blueprint $table) {
            $table->dropColumn('es_admin_plataforma');
        });
    }
};