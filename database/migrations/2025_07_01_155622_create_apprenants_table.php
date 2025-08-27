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
        Schema::create('apprenants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('utilisateur_id')->constrained('utilisateurs')->onDelete('cascade');
            $table->foreignId('niveau_id')->nullable()->constrained('niveaux')->onDelete('set null');
            // Etape 2 : besoins de formation
            $table->string('connaissance_adis')->nullable(); // réseaux sociaux, publicité, bouche à oreille
            $table->boolean('formation_adis')->nullable(); // déjà participé formation ADIS ?
            $table->boolean('formation_autre')->nullable(); // déjà formation ailleurs ?
            $table->enum('niveau_coran', ['Débutant', 'Intermédiaire', 'Avancé'])->nullable();
            $table->enum('niveau_arabe', ['Débutant', 'Intermédiaire', 'Avancé'])->nullable();
            $table->boolean('connaissance_tomes_medine')->nullable();
            $table->json('tomes_medine_etudies')->nullable(); // Tome 1, 2, 3, 4, Aucun
            $table->json('disciplines_souhaitees')->nullable(); // choix multiples
            $table->json('attentes')->nullable(); // attentes à court, moyen, long terme
            $table->boolean('formateur_domicile')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apprenants');
    }
};
