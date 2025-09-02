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
        Schema::table('liens_sociaux', function (Blueprint $table) {
            // Supprimer la colonne id existante
            $table->dropColumn('id');
        });

        Schema::table('liens_sociaux', function (Blueprint $table) {
            // Recréer la colonne id avec auto-increment
            $table->id()->first();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('liens_sociaux', function (Blueprint $table) {
            // Supprimer la colonne id auto-increment
            $table->dropColumn('id');
        });

        Schema::table('liens_sociaux', function (Blueprint $table) {
            // Recréer la colonne id sans auto-increment
            $table->bigInteger('id')->unsigned()->first();
        });
    }
};
