<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // ID de Rol como 1 BYTE (unsignedTinyInteger): 0 = admin, 1 = jugador (por defecto 1)
            $table->unsignedTinyInteger('role_id')->default(1);

            // 1 byte cada uno para el progreso de los juegos (0 o 1)
            $table->unsignedTinyInteger('juego1')->default(0); // Alquimia
            $table->unsignedTinyInteger('juego2')->default(0); // Zodiaco
            $table->unsignedTinyInteger('juego3')->default(0); // Música
            $table->unsignedTinyInteger('juego4')->default(0); // Quinto Elemento

            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};