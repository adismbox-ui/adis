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
        Schema::create('learner_desired_disciplines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learner_profile_id')->constrained('learner_profiles');
            $table->foreignId('discipline_id')->constrained('disciplines');
            $table->integer('priority_level')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learner_desired_disciplines');
    }
};
