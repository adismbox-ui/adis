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
            $table->enum('statut', [
                'en_attente', 
                'validee', 
                'en_attente_formateur', 
                'refusee',
                'acceptee_formateur',
                'refusee_formateur'
            ])->default('en_attente')->change();
        });

        // Nouvelle migration pour supprimer la colonne 'questions' de la table 'questionnaires'
        // database/migrations/2025_07_30_999999_remove_questions_column_from_questionnaires_table.php

        if (Schema::hasColumn('questionnaires', 'questions')) {
            Schema::table('questionnaires', function (Blueprint $table) {
                $table->dropColumn('questions');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('demandes_cours_maison', function (Blueprint $table) {
            $table->enum('statut', [
                'en_attente', 
                'validee', 
                'en_attente_formateur', 
                'refusee'
            ])->default('en_attente')->change();
        });

        if (!Schema::hasColumn('questionnaires', 'questions')) {
            Schema::table('questionnaires', function (Blueprint $table) {
                $table->text('questions')->nullable();
            });
        }
    }
};
