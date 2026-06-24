<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('corporate_id')->constrained()->cascadeOnDelete();
            $table->string('number_plate')->unique();
            $table->unsignedInteger('engine_capacity');
            $table->string('make');
            $table->string('model');
            $table->unsignedSmallInteger('year');
            $table->string('vehicle_type');
            $table->string('chassis_number')->nullable();
            $table->string('vin')->nullable();
            $table->string('fuel_type')->nullable();
            $table->string('owner_name')->nullable();
            $table->date('last_license_expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
