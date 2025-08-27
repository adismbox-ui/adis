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
        // Ajouter les champs de programmation à la table questionnaires
        Schema::table('questionnaires', function (Blueprint $table) {
            $table->unsignedBigInteger('session_id')->nullable()->after('module_id');
            $table->timestamp('date_envoi')->nullable()->after('session_id');
            $table->boolean('envoye')->default(false)->after('date_envoi');
            
            $table->foreign('session_id')->references('id')->on('sessions_formation')->onDelete('cascade');
        });

        // Ajouter les champs de programmation à la table documents
        Schema::table('documents', function (Blueprint $table) {
            $table->unsignedBigInteger('session_id')->nullable()->after('module_id');
            $table->timestamp('date_envoi')->nullable()->after('session_id');
            $table->boolean('envoye')->default(false)->after('date_envoi');
            
            $table->foreign('session_id')->references('id')->on('sessions_formation')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Supprimer les champs de programmation de la table questionnaires
        Schema::table('questionnaires', function (Blueprint $table) {
            $table->dropForeign(['session_id']);
            $table->dropColumn(['session_id', 'date_envoi', 'envoye']);
        });

        // Supprimer les champs de programmation de la table documents
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['session_id']);
            $table->dropColumn(['session_id', 'date_envoi', 'envoye']);
        });
    }
};
