<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('corporate_id')->nullable()->after('id')->index();
            $table->string('role')->default('corporate_admin')->after('email');
            $table->string('phone_number')->nullable()->after('role');
            $table->boolean('is_approved')->default(false)->after('phone_number');
            $table->timestamp('approved_at')->nullable()->after('is_approved');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['corporate_id']);
            $table->dropColumn('corporate_id');
            $table->dropColumn(['role', 'phone_number', 'is_approved', 'approved_at']);
        });
    }
};
