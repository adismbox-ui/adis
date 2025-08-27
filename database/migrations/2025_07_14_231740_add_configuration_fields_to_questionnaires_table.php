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
        Schema::table('questionnaires', function (Blueprint $table) {
            $table->integer('minutes')->nullable()->after('description');
            $table->integer('semaine')->nullable()->after('minutes');
            $table->enum('type_devoir', ['hebdomadaire', 'mensuel', 'final'])->nullable()->after('semaine');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questionnaires', function (Blueprint $table) {
            $table->dropColumn(['minutes', 'semaine', 'type_devoir']);
        });
    }
};
