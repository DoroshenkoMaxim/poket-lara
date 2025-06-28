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
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('symbol')->unique(); // AED/CNY, USD/EUR и т.д.
            $table->string('label'); // Полное название валютной пары
            $table->integer('payout')->nullable(); // Процент выплаты (92, 89 и т.д.)
            $table->boolean('is_active')->default(true); // Активна ли валютная пара
            $table->boolean('is_otc')->default(false); // OTC валюта или нет
            $table->text('flags')->nullable(); // JSON с кодами флагов валют
            $table->timestamp('last_updated')->nullable(); // Когда обновлялись данные
            $table->timestamps();
            
            $table->index('is_active');
            $table->index('payout');
            $table->index('last_updated');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
}; 