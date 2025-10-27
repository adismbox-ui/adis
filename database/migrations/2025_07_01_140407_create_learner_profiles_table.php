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
        Schema::create('learner_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('how_discovered_adis')->nullable();
            $table->boolean('previous_adis_training');
            $table->boolean('previous_arabic_training');
            $table->string('quran_reading_level');
            $table->string('arabic_level');
            $table->boolean('knows_medina_books');
            $table->boolean('wants_home_trainer');
            $table->text('short_term_goals')->nullable();
            $table->text('medium_term_goals')->nullable();
            $table->text('long_term_goals')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learner_profiles');
    }
};
