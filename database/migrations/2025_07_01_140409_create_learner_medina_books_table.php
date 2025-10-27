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
        Schema::create('learner_medina_books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learner_profile_id')->constrained('learner_profiles');
            $table->foreignId('medina_book_id')->constrained('medina_books');
            $table->boolean('completed');
            $table->date('completion_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learner_medina_books');
    }
};
