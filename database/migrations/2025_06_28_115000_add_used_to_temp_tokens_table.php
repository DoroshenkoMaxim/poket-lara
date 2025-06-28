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
        Schema::table('temp_tokens', function (Blueprint $table) {
            $table->boolean('used')->default(false)->after('expires_at');
            $table->index(['used']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('temp_tokens', function (Blueprint $table) {
            $table->dropIndex(['used']);
            $table->dropColumn('used');
        });
    }
}; 