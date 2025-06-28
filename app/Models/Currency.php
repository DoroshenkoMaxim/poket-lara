<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = [
        'symbol',
        'label',
        'payout',
        'is_active',
        'is_otc',
        'flags',
        'last_updated'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_otc' => 'boolean',
        'flags' => 'array',
        'last_updated' => 'datetime'
    ];

    /**
     * Получить только активные валютные пары
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Получить только OTC валютные пары
     */
    public function scopeOtc($query)
    {
        return $query->where('is_otc', true);
    }

    /**
     * Получить валютные пары с определенным минимальным процентом выплаты
     */
    public function scopeMinPayout($query, $minPayout)
    {
        return $query->where('payout', '>=', $minPayout);
    }

    /**
     * Получить процент выплаты в виде строки с знаком +
     */
    protected function payoutFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->payout ? '+' . $this->payout . '%' : null,
        );
    }

    /**
     * Определить является ли валютная пара OTC по названию
     */
    public function setLabelAttribute($value)
    {
        $this->attributes['label'] = $value;
        $this->attributes['is_otc'] = str_contains($value, 'OTC');
    }

    /**
     * Получить символ валютной пары без OTC
     */
    protected function symbolClean(): Attribute
    {
        return Attribute::make(
            get: fn () => str_replace(' OTC', '', $this->label),
        );
    }

    /**
     * Создать или обновить валютную пару
     */
    public static function createOrUpdate($data)
    {
        return self::updateOrCreate(
            ['symbol' => $data['symbol']],
            array_merge($data, ['last_updated' => now()])
        );
    }
} 