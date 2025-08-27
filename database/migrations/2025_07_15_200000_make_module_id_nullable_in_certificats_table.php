<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('certificats', function (Blueprint $table) {
            $table->dropForeign(['module_id']);
            $table->unsignedBigInteger('module_id')->nullable()->change();
            $table->foreign('module_id')->references('id')->on('modules')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('certificats', function (Blueprint $table) {
            $table->dropForeign(['module_id']);
            $table->unsignedBigInteger('module_id')->nullable(false)->change();
            $table->foreign('module_id')->references('id')->on('modules')->onDelete('cascade');
        });
    }
}; 