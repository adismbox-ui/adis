<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('demandes_cours_maison', function (Blueprint $table) {
    if (!Schema::hasColumn('demandes_cours_maison', 'formateur_id')) {
        $table->unsignedBigInteger('formateur_id')->nullable()->after('user_id');
        $table->foreign('formateur_id')->references('id')->on('formateurs')->onDelete('set null');
    }
});
    }

    public function down()
    {
        Schema::table('demandes_cours_maison', function (Blueprint $table) {
            $table->dropForeign(['formateur_id']);
            $table->dropColumn(['formateur_id', 'statut']);
        });
    }
};
