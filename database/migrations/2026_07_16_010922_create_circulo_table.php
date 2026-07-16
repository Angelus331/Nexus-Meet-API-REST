<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('circulo', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->string('tipo', 20); // 'estudio' | 'comida'
            $table->string('curso', 100)->nullable(); // solo aplica si tipo = 'estudio'
            $table->foreignId('creador_id')->constrained('usuario')->restrictOnDelete();
            $table->integer('max_miembros')->default(10);
            $table->string('codigo_invitacion', 20)->unique()->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index('tipo', 'idx_circulo_tipo');
            $table->index('creador_id', 'idx_circulo_creador');
        });

        DB::statement("ALTER TABLE circulo ADD CONSTRAINT circulo_tipo_check CHECK (tipo IN ('estudio', 'comida'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('circulo');
    }
};
