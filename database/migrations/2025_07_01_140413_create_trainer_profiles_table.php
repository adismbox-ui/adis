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
        Schema::create('trainer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('how_discovered_adis')->nullable();
            $table->boolean('previous_adis_training');
            $table->boolean('previous_arabic_training');
            $table->string('quran_reading_level');
            $table->string('arabic_level');
            $table->string('french_level');
            $table->string('last_religious_degree');
            $table->string('last_general_degree');
            $table->string('religious_degree_file')->nullable();
            $table->string('general_degree_file')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainer_profiles');
    }
};
