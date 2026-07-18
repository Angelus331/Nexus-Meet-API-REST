<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reporte_mensaje', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mensaje_id')->constrained('mensaje_chat')->cascadeOnDelete();
            $table->foreignId('usuario_id')->constrained('usuario')->cascadeOnDelete(); // quien reportó
            $table->text('motivo')->nullable();
            $table->boolean('resuelto')->default(false);
            $table->timestamp('created_at')->useCurrent();

            // Un mismo usuario no puede reportar el mismo mensaje dos veces
            $table->unique(['mensaje_id', 'usuario_id']);
            $table->index('resuelto', 'idx_reporte_mensaje_resuelto');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reporte_mensaje');
    }
};