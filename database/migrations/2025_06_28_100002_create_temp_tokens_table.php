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
        Schema::create('temp_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('token', 64)->unique();
            $table->bigInteger('telegram_id');
            $table->string('click_id');
            $table->string('trader_id');
            $table->timestamp('expires_at');
            $table->timestamps();
            
            $table->index(['token']);
            $table->index(['telegram_id']);
            $table->index(['expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_tokens');
    }
}; 