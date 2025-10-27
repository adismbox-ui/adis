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
        Schema::table('notifications', function (Blueprint $table) {
            if (!Schema::hasColumn('notifications', 'type')) {
                $table->string('type')->nullable()->after('id');
            }
            if (!Schema::hasColumn('notifications', 'icon')) {
                $table->string('icon')->nullable()->after('message');
            }
            if (!Schema::hasColumn('notifications', 'color')) {
                $table->string('color')->nullable()->after('icon');
            }
            if (!Schema::hasColumn('notifications', 'data')) {
                $table->json('data')->nullable()->after('color');
            }
            if (!Schema::hasColumn('notifications', 'action_url')) {
                $table->string('action_url')->nullable()->after('admin_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn(['type', 'icon', 'color', 'data', 'action_url']);
        });
    }
};
