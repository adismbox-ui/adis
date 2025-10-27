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
        Schema::create('appel_a_projets', function (Blueprint $table) {
            $table->id();
            $table->string('reference');
            $table->string('intitule');
            $table->string('domaine');
            $table->date('date_limite_soumission');
            $table->enum('etat', ['ouvert', 'cloture']);
            $table->text('details_offre')->nullable();
            $table->decimal('montant_estimatif', 12, 2)->nullable();
            $table->string('beneficiaires')->nullable();
            $table->string('partenaire_retenu')->nullable();
            $table->date('date_cloture')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appel_a_projets');
    }
};
