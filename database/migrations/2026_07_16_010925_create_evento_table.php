<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evento', function (Blueprint $table) {
            $table->id();
            $table->foreignId('circulo_id')->constrained('circulo')->cascadeOnDelete();
            $table->string('tipo', 30); // 'sesion_estudio' | 'turno_comida'
            $table->string('titulo', 150);
            $table->text('descripcion')->nullable();
            $table->timestamp('fecha_hora');
            $table->string('ubicacion', 200)->nullable();
            $table->foreignId('responsable_id')->nullable()->constrained('usuario')->nullOnDelete(); // encargado del turno de comida
            $table->string('estado', 20)->default('programado');
            $table->timestamps();

            $table->index(['circulo_id', 'fecha_hora'], 'idx_evento_circulo_fecha');
        });

        DB::statement("ALTER TABLE evento ADD CONSTRAINT evento_tipo_check CHECK (tipo IN ('sesion_estudio', 'turno_comida'))");
        DB::statement("ALTER TABLE evento ADD CONSTRAINT evento_estado_check CHECK (estado IN ('programado', 'en_curso', 'finalizado', 'cancelado'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('evento');
    }
};
