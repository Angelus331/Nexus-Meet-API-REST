<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla auxiliar requerida por el módulo de notificaciones push (Flutter)
        Schema::create('notificacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuario')->cascadeOnDelete();
            $table->foreignId('circulo_id')->nullable()->constrained('circulo')->cascadeOnDelete();
            $table->string('tipo', 30); // mensaje_nuevo, evento_proximo, turno_asignado, etc.
            $table->string('titulo', 150);
            $table->text('contenido')->nullable();
            $table->boolean('leida')->default(false);
            $table->timestamp('created_at')->useCurrent();

            $table->index(['usuario_id', 'leida'], 'idx_notificacion_usuario');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificacion');
    }
};

