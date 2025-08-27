<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('niveaux', function (Blueprint $table) {
            if (!Schema::hasColumn('niveaux', 'formateur_id')) {
                $table->foreignId('formateur_id')->nullable()->after('actif')
                    ->constrained('formateurs')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('niveaux', function (Blueprint $table) {
            if (Schema::hasColumn('niveaux', 'formateur_id')) {
                $table->dropConstrainedForeignId('formateur_id');
            }
        });
    }
};

