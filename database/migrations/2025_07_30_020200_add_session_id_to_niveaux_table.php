<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('niveaux', function (Blueprint $table) {
            if (!Schema::hasColumn('niveaux', 'session_id')) {
                $table->foreignId('session_id')->nullable()->after('lien_meet')
                    ->constrained('sessions_formation')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('niveaux', function (Blueprint $table) {
            if (Schema::hasColumn('niveaux', 'session_id')) {
                $table->dropConstrainedForeignId('session_id');
            }
        });
    }
};

