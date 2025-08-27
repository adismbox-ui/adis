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
        Schema::table('documents', function (Blueprint $table) {
            if (!Schema::hasColumn('documents', 'session_id')) {
                $table->foreignId('session_id')->nullable()->after('niveau_id')->constrained('sessions_formation')->onDelete('set null');
            }
            if (!Schema::hasColumn('documents', 'date_envoi')) {
                $table->datetime('date_envoi')->nullable()->after('session_id');
            }
            if (!Schema::hasColumn('documents', 'envoye')) {
                $table->boolean('envoye')->default(false)->after('date_envoi');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['session_id']);
            $table->dropColumn(['session_id', 'date_envoi', 'envoye']);
        });
    }
};
