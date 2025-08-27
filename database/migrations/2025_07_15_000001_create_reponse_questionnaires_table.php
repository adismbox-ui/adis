<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reponse_questionnaires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apprenant_id')->constrained('apprenants')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->string('reponse');
            $table->timestamps();
            $table->unique(['apprenant_id', 'question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reponse_questionnaires');
    }
}; 