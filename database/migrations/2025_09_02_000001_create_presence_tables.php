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
        Schema::create('presence_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('formateur_id');
            $table->string('nom')->nullable();
            $table->text('commentaire')->nullable();
            $table->boolean('is_open')->default(true);
            $table->timestamps();

            $table->foreign('formateur_id')->references('id')->on('formateurs')->onDelete('cascade');
            $table->index(['formateur_id', 'is_open']);
        });

        Schema::create('presence_marks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('presence_request_id');
            $table->unsignedBigInteger('apprenant_id');
            $table->timestamp('present_at')->useCurrent();
            $table->timestamps();

            $table->foreign('presence_request_id')->references('id')->on('presence_requests')->onDelete('cascade');
            $table->foreign('apprenant_id')->references('id')->on('apprenants')->onDelete('cascade');
            $table->unique(['presence_request_id', 'apprenant_id']);
            $table->index('present_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presence_marks');
        Schema::dropIfExists('presence_requests');
    }
};

