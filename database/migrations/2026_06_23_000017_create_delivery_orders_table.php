<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('license_disk_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('credit_application_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('rider_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('delivery_address');
            $table->string('contact_mobile');
            $table->string('landmark')->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_orders');
    }
};
