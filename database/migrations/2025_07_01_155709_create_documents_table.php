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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->string('type')->nullable();
            $table->string('fichier');
            $table->string('audio')->nullable();
            $table->foreignId('module_id')->nullable()->constrained('modules')->onDelete('set null');
            $table->foreignId('certificat_id')->nullable()->constrained('certificats')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
