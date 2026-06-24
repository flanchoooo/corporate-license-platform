<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cbz_direct_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('corporate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('wallet_id')->constrained()->cascadeOnDelete();
            $table->foreignId('initiated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('payment_reference')->unique();
            $table->string('bank_reference')->nullable();
            $table->string('payer_name');
            $table->string('account_number')->nullable();
            $table->bigInteger('amount_cents');
            $table->string('status')->default('pending');
            $table->json('provider_payload')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cbz_direct_payments');
    }
};
