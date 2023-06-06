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
        Schema::create('log_receiver', function (Blueprint $table) {
            $table->foreignId('log_id')->constrained()->onDelete('cascade');
            $table->foreignId('receiver_id')->constrained()->onDelete('cascade');
            $table->unique(['log_id', 'receiver_id']);
            $table->boolean('send_status');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_receiver');
    }
};
