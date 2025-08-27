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
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('description')->nullable();
            $table->foreignId('formateur_id')->nullable()->constrained('formateurs')->onDelete('set null');
            $table->foreignId('niveau_id')->nullable()->constrained('niveaux')->onDelete('set null');
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->string('horaire')->nullable();
            $table->string('lien')->nullable(); // lien zoom ou autre
            $table->string('discipline')->nullable();
            $table->string('support')->nullable(); // chemin du PDF
            $table->string('audio')->nullable(); // chemin du fichier audio
            $table->integer('prix')->nullable(); // en FCFA
            $table->boolean('certificat')->default(false); // oui/non
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
