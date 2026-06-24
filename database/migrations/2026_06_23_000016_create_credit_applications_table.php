<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('credit_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quote_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('corporate_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('surname');
            $table->string('national_id');
            $table->string('mobile_number');
            $table->string('address');
            $table->string('delivery_address');
            $table->string('delivery_mobile');
            $table->string('delivery_landmark')->nullable();
            $table->string('status')->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credit_applications');
    }
};
