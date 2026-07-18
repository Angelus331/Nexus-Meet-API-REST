<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usuario', function (Blueprint $table) {
            // Token del dispositivo (Flutter/FCM) para mandar notificaciones push.
            // Una sola columna = un dispositivo activo a la vez (el del último login).
            $table->string('fcm_token', 255)->nullable()->after('telefono');
        });
    }

    public function down(): void
    {
        Schema::table('usuario', function (Blueprint $table) {
            $table->dropColumn('fcm_token');
        });
    }
};