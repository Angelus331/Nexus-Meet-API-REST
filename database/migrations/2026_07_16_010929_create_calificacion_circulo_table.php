<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calificacion_circulo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('circulo_id')->constrained('circulo')->cascadeOnDelete();
            $table->foreignId('usuario_id')->constrained('usuario')->cascadeOnDelete();
            $table->smallInteger('puntuacion');
            $table->text('comentario')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['circulo_id', 'usuario_id']);
        });

        DB::statement("ALTER TABLE calificacion_circulo ADD CONSTRAINT calificacion_puntuacion_check CHECK (puntuacion BETWEEN 1 AND 5)");
    }

    public function down(): void
    {
        Schema::dropIfExists('calificacion_circulo');
    }
};
