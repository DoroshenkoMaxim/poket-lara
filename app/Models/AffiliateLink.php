<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AffiliateLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'click_id',
        'telegram_id',
        'first_name',
        'last_name',
        'username',
        'language_code',
    ];

    protected $casts = [
        'telegram_id' => 'integer',
    ];

    /**
     * Получить регистрации по этой ссылке
     */
    public function registrations()
    {
        return $this->hasMany(Registration::class, 'click_id', 'click_id');
    }

    /**
     * Получить временные токены по этой ссылке
     */
    public function tempTokens()
    {
        return $this->hasMany(TempToken::class, 'click_id', 'click_id');
    }

    /**
     * Найти ссылку по click_id
     */
    public static function findByClickId(string $clickId): ?self
    {
        return static::where('click_id', $clickId)->first();
    }

    /**
     * Создать новую партнерскую ссылку
     */
    public static function createLink(int $telegramId, array $userInfo = []): self
    {
        $clickId = uniqid('click_' . $telegramId . '_', true);
        
        return static::create([
            'click_id' => $clickId,
            'telegram_id' => $telegramId,
            'first_name' => $userInfo['first_name'] ?? null,
            'last_name' => $userInfo['last_name'] ?? null,
            'username' => $userInfo['username'] ?? null,
            'language_code' => $userInfo['language_code'] ?? null,
        ]);
    }
} 