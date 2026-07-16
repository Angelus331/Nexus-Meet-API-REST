<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mensaje_chat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('circulo_id')->constrained('circulo')->cascadeOnDelete();
            $table->foreignId('usuario_id')->constrained('usuario')->cascadeOnDelete();
            $table->text('contenido');
            $table->string('tipo', 20)->default('texto');
            $table->boolean('editado')->default(false);
            $table->timestamp('created_at')->useCurrent();
            // Nota: la tabla original solo tiene created_at, sin updated_at

            $table->index(['circulo_id', 'created_at'], 'idx_mensaje_circulo_fecha');
        });

        DB::statement("ALTER TABLE mensaje_chat ADD CONSTRAINT mensaje_chat_tipo_check CHECK (tipo IN ('texto', 'imagen', 'archivo', 'sistema'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('mensaje_chat');
    }
};

