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
        Schema::create('utilisateurs', function (Blueprint $table) {
            $table->id();
            $table->string('prenom');
            $table->string('nom');
            $table->enum('sexe', ['Homme', 'Femme']);
            $table->enum('categorie', ['Enfant', 'Etudiant', 'Professionnel', 'Enseignant'])->nullable();
            $table->string('telephone', 20)->nullable();
            $table->string('email')->unique();
            $table->string('mot_de_passe');
            $table->enum('type_compte', ['admin', 'assistant', 'formateur', 'apprenant']);
            $table->boolean('actif')->default(true);
            $table->timestamp('email_verified_at')->nullable();
            $table->text('infos_complementaires')->nullable();
            $table->string('verification_token', 100)->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('utilisateurs');
    }
};
