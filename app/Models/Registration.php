<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    use HasFactory;

    protected $fillable = [
        'click_id',
        'trader_id',
        'country',
        'promo',
        'device_type',
        'os_version',
        'browser',
        'link_type',
        'site_id',
        'sub_id1',
        'cid',
        'date_time',
    ];

    /**
     * Получить партнерскую ссылку
     */
    public function affiliateLink()
    {
        return $this->belongsTo(AffiliateLink::class, 'click_id', 'click_id');
    }

    /**
     * Получить временные токены для этой регистрации
     */
    public function tempTokens()
    {
        return $this->hasMany(TempToken::class, 'click_id', 'click_id')
                    ->where('trader_id', $this->trader_id);
    }

    /**
     * Найти регистрацию по click_id и trader_id
     */
    public static function findByIds(string $clickId, string $traderId): ?self
    {
        return static::where('click_id', $clickId)
                     ->where('trader_id', $traderId)
                     ->first();
    }

    /**
     * Получить регистрацию с данными Telegram
     */
    public static function getWithTelegram(string $clickId, string $traderId): ?array
    {
        $registration = static::findByIds($clickId, $traderId);
        
        if (!$registration) {
            return null;
        }

        $affiliateLink = $registration->affiliateLink;
        
        if (!$affiliateLink) {
            return null;
        }

        return array_merge($registration->toArray(), [
            'telegram_id' => $affiliateLink->telegram_id
        ]);
    }
} 