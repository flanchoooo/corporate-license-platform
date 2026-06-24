<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bot_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_key')->unique();
            $table->string('channel')->default('web');
            $table->string('current_menu')->nullable();
            $table->string('number_plate')->nullable();
            $table->string('state')->default('menu');
            $table->json('context')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bot_sessions');
    }
};
