<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nis')->nullable()->unique()->after('name');
            $table->boolean('is_active')->default(false)->after('email_verified_at');
            $table->string('pending_status')->default('pending')->after('is_active');
            $table->boolean('is_admin')->default(false)->after('pending_status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nis', 'is_active', 'pending_status', 'is_admin']);
        });
    }
};

