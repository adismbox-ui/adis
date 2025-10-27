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
        Schema::create('sessions_formation', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->text('description')->nullable();
            $table->foreignId('niveau_id')->nullable()->constrained('niveaux')->onDelete('cascade');
            $table->date('date_debut');
            $table->date('date_fin');
            $table->time('heure_debut')->nullable();
            $table->time('heure_fin')->nullable();
            $table->string('jour_semaine')->nullable(); // Lundi, Mardi, etc.
            $table->integer('duree_seance_minutes')->default(60);
            $table->integer('nombre_seances')->default(1);
            $table->decimal('prix', 10, 2)->nullable();
            $table->integer('places_max')->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions_formation');
    }
}; 