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
        Schema::table('notifications', function (Blueprint $table) {
            // Supprimer l'ancienne contrainte de clé étrangère
            $table->dropForeign(['user_id']);
            
            // Supprimer l'ancienne contrainte de clé étrangère pour admin_id
            $table->dropForeign(['admin_id']);
        });

        Schema::table('notifications', function (Blueprint $table) {
            // Ajouter la nouvelle contrainte de clé étrangère pour user_id
            $table->foreign('user_id')->references('id')->on('utilisateurs')->onDelete('cascade');
            
            // Ajouter la nouvelle contrainte de clé étrangère pour admin_id
            $table->foreign('admin_id')->references('id')->on('utilisateurs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Supprimer les nouvelles contraintes
            $table->dropForeign(['user_id']);
            $table->dropForeign(['admin_id']);
        });

        Schema::table('notifications', function (Blueprint $table) {
            // Restaurer les anciennes contraintes
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('admin_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
