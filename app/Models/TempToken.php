<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TempToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'token',
        'telegram_id',
        'click_id',
        'trader_id',
        'expires_at',
    ];

    protected $casts = [
        'telegram_id' => 'integer',
        'expires_at' => 'datetime',
    ];

    /**
     * Получить партнерскую ссылку
     */
    public function affiliateLink()
    {
        return $this->belongsTo(AffiliateLink::class, 'click_id', 'click_id');
    }

    /**
     * Получить регистрацию
     */
    public function registration()
    {
        return $this->belongsTo(Registration::class, 'click_id', 'click_id')
                    ->where('trader_id', $this->trader_id);
    }

    /**
     * Создать новый временный токен
     */
    public static function createToken(int $telegramId, string $clickId, string $traderId): self
    {
        $token = bin2hex(random_bytes(32));
        
        return static::create([
            'token' => $token,
            'telegram_id' => $telegramId,
            'click_id' => $clickId,
            'trader_id' => $traderId,
            'expires_at' => Carbon::now()->addHours(24),
        ]);
    }

    /**
     * Валидировать токен
     */
    public static function validateToken(string $token): ?self
    {
        return static::where('token', $token)
                     ->where('expires_at', '>', now())
                     ->first();
    }

    /**
     * Очистить истекшие токены
     */
    public static function cleanExpired(): int
    {
        return static::where('expires_at', '<', now())->delete();
    }

    /**
     * Проверить, истек ли токен
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }
} 