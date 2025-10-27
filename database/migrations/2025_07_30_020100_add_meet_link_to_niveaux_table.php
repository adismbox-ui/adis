<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('niveaux', function (Blueprint $table) {
            if (!Schema::hasColumn('niveaux', 'lien_meet')) {
                $table->string('lien_meet', 255)->nullable()->after('formateur_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('niveaux', function (Blueprint $table) {
            if (Schema::hasColumn('niveaux', 'lien_meet')) {
                $table->dropColumn('lien_meet');
            }
        });
    }
};

