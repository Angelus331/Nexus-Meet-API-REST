<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuario', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('apellido', 100);
            $table->string('correo', 150)->unique();
            $table->string('password_hash');
            $table->string('google_id', 100)->unique()->nullable(); // login con Google OAuth
            $table->text('foto_perfil_url')->nullable();
            $table->string('facultad', 100)->nullable();
            $table->string('carrera', 100)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->timestamp('email_verificado_en')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps(); // created_at, updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuario');
    }
};
