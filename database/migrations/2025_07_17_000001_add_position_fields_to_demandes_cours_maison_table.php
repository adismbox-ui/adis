<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('demandes_cours_maison', function (Blueprint $table) {
            $table->string('ville', 100)->nullable();
            $table->string('commune', 100)->nullable();
            $table->string('quartier', 100)->nullable();
            $table->string('numero', 20)->nullable();
        });
    }
    public function down() {
        Schema::table('demandes_cours_maison', function (Blueprint $table) {
            $table->dropColumn(['ville', 'commune', 'quartier', 'numero']);
        });
    }
};
