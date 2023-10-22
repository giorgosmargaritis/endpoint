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
        Schema::create('logs_receivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('log_id')->constrained()->onDelete('cascade');
            $table->foreignId('endpoints_receivers_id')->constrained()->onDelete('cascade');
            $table->unique(['log_id', 'endpoints_receivers_id']);
            $table->longText('transformed_data');
            $table->boolean('status');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs_receivers');
    }
};
