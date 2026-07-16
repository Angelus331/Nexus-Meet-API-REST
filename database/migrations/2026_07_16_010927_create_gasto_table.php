<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gasto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('circulo_id')->constrained('circulo')->cascadeOnDelete();
            $table->foreignId('evento_id')->nullable()->constrained('evento')->nullOnDelete();
            $table->foreignId('usuario_pagador_id')->constrained('usuario')->restrictOnDelete();
            $table->decimal('monto_total', 10, 2);
            $table->string('descripcion', 255)->nullable();
            $table->date('fecha')->default(DB::raw('CURRENT_DATE'));
            $table->timestamp('created_at')->useCurrent();

            $table->index('circulo_id', 'idx_gasto_circulo');
        });

        DB::statement("ALTER TABLE gasto ADD CONSTRAINT gasto_monto_check CHECK (monto_total >= 0)");
    }

    public function down(): void
    {
        Schema::dropIfExists('gasto');
    }
};
