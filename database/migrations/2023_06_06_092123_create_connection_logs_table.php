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
        Schema::create('connections_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('connection_id')->constrained()->onDelete('cascade');
            $table->foreignId('log_id')->constrained()->onDelete('cascade');
            $table->unique(['connection_id', 'log_id']);
            $table->string('campaign_id')->index('campaign_id');
            $table->string('leadgen_id')->index('leadgen_id');
            $table->longText('transformed_data');
            $table->tinyInteger('status')->unsigned();
            $table->boolean('is_test');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('connections_logs');
    }
};
