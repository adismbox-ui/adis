<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('demandes_cours_maison', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('module');
            $table->unsignedInteger('nombre_enfants');
            $table->text('message');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('utilisateurs')->onDelete('cascade');
        });
    }
    public function down() {
        Schema::dropIfExists('demandes_cours_maison');
    }
};
