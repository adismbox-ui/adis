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
        Schema::table('modules', function (Blueprint $table) {
            if (!Schema::hasColumn('modules', 'prix')) {
                $table->integer('prix')->nullable();
            }
            if (!Schema::hasColumn('modules', 'description')) {
                $table->text('description')->nullable();
            }
            if (!Schema::hasColumn('modules', 'certificat')) {
                $table->boolean('certificat')->default(false);
            }
            if (!Schema::hasColumn('modules', 'date_debut')) {
                $table->date('date_debut')->nullable();
            }
            if (!Schema::hasColumn('modules', 'date_fin')) {
                $table->date('date_fin')->nullable();
            }
            if (!Schema::hasColumn('modules', 'formateur_id')) {
                $table->unsignedBigInteger('formateur_id')->nullable();
            }
            if (!Schema::hasColumn('modules', 'lien')) {
                $table->string('lien')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            if (Schema::hasColumn('modules', 'prix')) {
                $table->dropColumn('prix');
            }
            if (Schema::hasColumn('modules', 'description')) {
                $table->dropColumn('description');
            }
            if (Schema::hasColumn('modules', 'certificat')) {
                $table->dropColumn('certificat');
            }
            if (Schema::hasColumn('modules', 'date_debut')) {
                $table->dropColumn('date_debut');
            }
            if (Schema::hasColumn('modules', 'date_fin')) {
                $table->dropColumn('date_fin');
            }
            if (Schema::hasColumn('modules', 'formateur_id')) {
                $table->dropColumn('formateur_id');
            }
            if (Schema::hasColumn('modules', 'lien')) {
                $table->dropColumn('lien');
            }
        });
    }
};
