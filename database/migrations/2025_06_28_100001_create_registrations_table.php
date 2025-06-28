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
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->string('click_id');
            $table->string('trader_id');
            $table->string('country')->nullable();
            $table->string('promo')->nullable();
            $table->string('device_type')->nullable();
            $table->string('os_version')->nullable();
            $table->string('browser')->nullable();
            $table->string('link_type')->nullable();
            $table->string('site_id')->nullable();
            $table->string('sub_id1')->nullable();
            $table->string('cid')->nullable();
            $table->string('date_time')->nullable();
            $table->timestamps();
            
            $table->index(['click_id']);
            $table->index(['trader_id']);
            $table->unique(['click_id', 'trader_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
}; 