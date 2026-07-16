<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('miembro_circulo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('circulo_id')->constrained('circulo')->cascadeOnDelete();
            $table->foreignId('usuario_id')->constrained('usuario')->cascadeOnDelete();
            $table->string('rol', 20)->default('miembro');
            $table->string('estado', 20)->default('activo');
            $table->timestamp('fecha_union')->useCurrent();

            $table->unique(['circulo_id', 'usuario_id']);
            $table->index('usuario_id', 'idx_miembro_circulo_usuario');
            $table->index('circulo_id', 'idx_miembro_circulo_circulo');
        });

        DB::statement("ALTER TABLE miembro_circulo ADD CONSTRAINT miembro_circulo_rol_check CHECK (rol IN ('admin', 'miembro'))");
        DB::statement("ALTER TABLE miembro_circulo ADD CONSTRAINT miembro_circulo_estado_check CHECK (estado IN ('activo', 'inactivo', 'expulsado'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('miembro_circulo');
    }
};
