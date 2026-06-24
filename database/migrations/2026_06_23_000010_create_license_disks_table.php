<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('license_disks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('corporate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('quote_id')->constrained()->cascadeOnDelete();
            $table->string('reference_number')->unique();
            $table->bigInteger('radio_license_fee_cents')->default(0);
            $table->bigInteger('insurance_fee_cents')->default(0);
            $table->bigInteger('zinara_fee_cents')->default(0);
            $table->bigInteger('arrears_cents')->default(0);
            $table->bigInteger('total_paid_cents')->default(0);
            $table->date('valid_from');
            $table->date('valid_until');
            $table->text('qr_payload');
            $table->timestamp('issued_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('license_disks');
    }
};
