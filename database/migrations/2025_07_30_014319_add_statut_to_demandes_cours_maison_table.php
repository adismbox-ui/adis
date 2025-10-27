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
        Schema::table('demandes_cours_maison', function (Blueprint $table) {
            $table->enum('statut', ['en_attente', 'validee', 'en_attente_formateur', 'refusee'])->default('en_attente')->after('formateur_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('demandes_cours_maison', function (Blueprint $table) {
            $table->dropColumn('statut');
        });
    }
};
