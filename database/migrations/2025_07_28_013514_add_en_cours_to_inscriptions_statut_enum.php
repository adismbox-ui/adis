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
        Schema::table('inscriptions', function (Blueprint $table) {
            // Modifier l'enum pour ajouter 'en_cours'
            $table->enum('statut', ['en_attente', 'en_cours', 'valide', 'refuse'])->default('en_attente')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inscriptions', function (Blueprint $table) {
            // Revenir à l'ancien enum sans 'en_cours'
            $table->enum('statut', ['en_attente', 'valide', 'refuse'])->default('en_attente')->change();
        });
    }
};
