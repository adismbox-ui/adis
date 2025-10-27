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
        Schema::create('formateurs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('utilisateur_id')->constrained('utilisateurs')->onDelete('cascade');
            $table->string('specialite')->nullable();
            $table->boolean('valide')->default(false);
            $table->string('validation_token', 255)->nullable();
            $table->string('connaissance_adis')->nullable();
            $table->boolean('formation_adis')->nullable();
            $table->boolean('formation_autre')->nullable();
            $table->enum('niveau_coran', ['Moyen', 'Avancé'])->nullable();
            $table->enum('niveau_arabe', ['Moyen', 'Avancé'])->nullable();
            $table->enum('niveau_francais', ['Débutant', 'Moyen', 'Avancé'])->nullable();
            $table->string('diplome_religieux')->nullable();
            $table->string('diplome_general')->nullable();
            $table->string('fichier_diplome_religieux')->nullable(); // chemin du fichier
            $table->string('fichier_diplome_general')->nullable(); // chemin du fichier
            $table->string('ville')->nullable();
            $table->string('commune')->nullable();
            $table->string('quartier')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formateurs');
    }
};
