<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('connections_logs', function (Blueprint $table) {
            $table->string('campaign_id')->index('campaign_id')->after('log_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('connections_logs', function (Blueprint $table) {
            $table->dropColumn(['campaign_id']);
        });
    }
};
