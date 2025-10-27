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
        Schema::create('candidatures', function (Blueprint $table) {
            $table->id();
            $table->string('raison_sociale');
            $table->string('nom_responsable');
            $table->string('statut_juridique');
            $table->string('rccm');
            $table->string('contact');
            $table->string('site_web')->nullable();
            $table->string('reference_appel');
            $table->string('offre_technique_path');
            $table->string('offre_financiere_path');
            $table->string('justificatif_paiement_path');
            $table->string('references_path')->nullable();
            $table->boolean('declaration_honneur');
            $table->enum('statut', ['en_attente', 'acceptee', 'refusee'])->default('en_attente');
            $table->text('notes_admin')->nullable();
            $table->timestamps();
            
            // Index pour améliorer les performances
            $table->index('reference_appel');
            $table->index('statut');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidatures');
    }
};
