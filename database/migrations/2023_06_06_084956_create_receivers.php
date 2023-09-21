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
        Schema::create('receivers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('url');
            $table->bigInteger('endpoint_id')->unsigned();
            $table->bigInteger('authenticationmethod_id')->unsigned();
            $table->timestamps();

            $table->foreign('endpoint_id')->references('id')->on('endpoints')->onDelete('cascade');
            $table->foreign('authenticationmethod_id')->references('id')->on('authentication_methods')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receivers');
    }
};
