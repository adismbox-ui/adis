<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('demandes_cours_maison', function (Blueprint $table) {
            $table->foreignId('niveau_id')->nullable()->constrained('niveaux')->onDelete('set null');
        });
    }
    public function down() {
        Schema::table('demandes_cours_maison', function (Blueprint $table) {
            $table->dropForeign(['niveau_id']);
            $table->dropColumn('niveau_id');
        });
    }
}; 