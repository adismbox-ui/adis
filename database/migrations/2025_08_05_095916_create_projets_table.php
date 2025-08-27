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
        Schema::create('projets', function (Blueprint $table) {
            $table->id();
            $table->string('intitule');
            $table->string('beneficiaires');
            $table->text('objectif');
            $table->date('debut');
            $table->date('fin_prevue')->nullable();
            $table->integer('taux_avancement')->default(0);
            $table->string('responsable');
            $table->enum('statut', ['en_cours', 'realise', 'a_financer']);
            $table->decimal('montant_total', 12, 2)->nullable();
            $table->decimal('montant_collecte', 12, 2)->nullable();
            $table->decimal('reste_a_financer', 12, 2)->nullable();
            $table->date('date_limite')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projets');
    }
};
