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
        Schema::table('dons', function (Blueprint $table) {
            // Ajouter les colonnes manquantes
            if (!Schema::hasColumn('dons', 'telephone')) {
                $table->string('telephone')->nullable()->after('email_donateur');
            }
            if (!Schema::hasColumn('dons', 'type_don')) {
                $table->enum('type_don', ['ponctuel', 'mensuel'])->after('montant');
            }
            if (!Schema::hasColumn('dons', 'mode_paiement')) {
                $table->enum('mode_paiement', ['carte', 'virement', 'mobile'])->after('projet_id');
            }
            if (!Schema::hasColumn('dons', 'recu_demande')) {
                $table->boolean('recu_demande')->default(false)->after('mode_paiement');
            }
            if (!Schema::hasColumn('dons', 'statut')) {
                $table->enum('statut', ['en_attente', 'confirme', 'annule', 'refuse'])->default('en_attente')->after('message');
            }
            if (!Schema::hasColumn('dons', 'date_don')) {
                $table->datetime('date_don')->after('statut');
            }
            if (!Schema::hasColumn('dons', 'numero_reference')) {
                $table->string('numero_reference')->unique()->after('date_don');
            }
            if (!Schema::hasColumn('dons', 'transaction_id')) {
                $table->string('transaction_id')->nullable()->after('numero_reference');
            }
            if (!Schema::hasColumn('dons', 'paiement_confirme')) {
                $table->boolean('paiement_confirme')->default(false)->after('transaction_id');
            }
            if (!Schema::hasColumn('dons', 'date_confirmation')) {
                $table->datetime('date_confirmation')->nullable()->after('paiement_confirme');
            }
            if (!Schema::hasColumn('dons', 'notes_admin')) {
                $table->text('notes_admin')->nullable()->after('date_confirmation');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dons', function (Blueprint $table) {
            $table->dropColumn([
                'telephone',
                'type_don',
                'mode_paiement',
                'recu_demande',
                'statut',
                'date_don',
                'numero_reference',
                'transaction_id',
                'paiement_confirme',
                'date_confirmation',
                'notes_admin'
            ]);
        });
    }
};
