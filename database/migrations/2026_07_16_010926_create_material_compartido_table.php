<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('material_compartido', function (Blueprint $table) {
            $table->id();
            $table->foreignId('circulo_id')->constrained('circulo')->cascadeOnDelete();
            $table->foreignId('usuario_id')->constrained('usuario')->cascadeOnDelete();
            $table->string('nombre_archivo', 255);
            $table->text('url_archivo');
            $table->string('tipo_archivo', 50)->nullable(); // pdf, imagen, docx, etc.
            $table->unsignedBigInteger('tamano_bytes')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('circulo_id', 'idx_material_circulo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_compartido');
    }
};
