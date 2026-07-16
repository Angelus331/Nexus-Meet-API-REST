<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Necesaria para "dividir gastos entre compañeros"
        Schema::create('gasto_detalle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gasto_id')->constrained('gasto')->cascadeOnDelete();
            $table->foreignId('usuario_id')->constrained('usuario')->cascadeOnDelete();
            $table->decimal('monto_asignado', 10, 2);
            $table->boolean('pagado')->default(false);
            $table->timestamp('fecha_pago')->nullable();

            $table->unique(['gasto_id', 'usuario_id']);
        });

        DB::statement("ALTER TABLE gasto_detalle ADD CONSTRAINT gasto_detalle_monto_check CHECK (monto_asignado >= 0)");
    }

    public function down(): void
    {
        Schema::dropIfExists('gasto_detalle');
    }
};
