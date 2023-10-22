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
        Schema::create('log_data_facebook', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('log_id')->unsigned();
            $table->longText('data_received');
            $table->longText('data_requested')->nullable();
            $table->longText('data_requested_response')->nullable();
            $table->tinyInteger('data_requested_status')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('log_id')->references('id')->on('logs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_data_facebook');
    }
};
