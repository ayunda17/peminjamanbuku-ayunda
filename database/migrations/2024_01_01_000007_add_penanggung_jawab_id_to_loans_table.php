<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->foreignId('penanggung_jawab_id')
                ->nullable()
                ->after('member_id')
                ->constrained('penanggung_jawabs')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropForeign(['penanggung_jawab_id']);
            $table->dropColumn('penanggung_jawab_id');
        });
    }
};
