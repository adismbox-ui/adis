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
        Schema::table('sessions_formation', function (Blueprint $table) {
            $table->foreignId('formateur_id')->nullable()->after('niveau_id')->constrained('formateurs')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sessions_formation', function (Blueprint $table) {
            $table->dropForeign(['formateur_id']);
            $table->dropColumn('formateur_id');
        });
    }
};
