<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('dons', function (Blueprint $table) {
            $table->id();
            $table->string('nom_donateur');
            $table->string('email_donateur');
            $table->string('telephone')->nullable();
            $table->decimal('montant', 12, 2);
            $table->enum('type_don', ['ponctuel', 'mensuel']);
            $table->foreignId('projet_id')->nullable()->constrained('projets')->onDelete('set null');
            $table->enum('mode_paiement', ['carte', 'virement', 'mobile']);
            $table->boolean('recu_demande')->default(false);
            $table->text('message')->nullable();
            $table->enum('statut', ['en_attente', 'confirme', 'annule', 'refuse'])->default('en_attente');
            $table->datetime('date_don');
            $table->string('numero_reference')->unique();
            $table->string('transaction_id')->nullable();
            $table->boolean('paiement_confirme')->default(false);
            $table->datetime('date_confirmation')->nullable();
            $table->text('notes_admin')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('dons');
    }
};