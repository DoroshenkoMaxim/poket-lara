<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationQueue extends Model
{
    use HasFactory;

    protected $table = 'notification_queue';

    protected $fillable = [
        'telegram_id',
        'message',
        'data',
        'status',
        'attempts',
        'sent_at',
    ];

    protected $casts = [
        'telegram_id' => 'integer',
        'data' => 'array',
        'attempts' => 'integer',
        'sent_at' => 'datetime',
    ];

    /**
     * Добавить уведомление в очередь
     */
    public static function addNotification(int $telegramId, string $message, array $data = []): self
    {
        return static::create([
            'telegram_id' => $telegramId,
            'message' => $message,
            'data' => $data,
            'status' => 'pending',
            'attempts' => 0,
        ]);
    }

    /**
     * Получить ожидающие уведомления
     */
    public static function getPending(): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('status', 'pending')
                     ->orderBy('created_at')
                     ->get();
    }

    /**
     * Отметить как отправленное
     */
    public function markAsSent(): bool
    {
        return $this->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    /**
     * Отметить как неудачное
     */
    public function markAsFailed(): bool
    {
        return $this->update([
            'status' => 'failed',
            'attempts' => $this->attempts + 1,
        ]);
    }

    /**
     * Сбросить статус для повторной попытки
     */
    public function resetForRetry(): bool
    {
        return $this->update([
            'status' => 'pending',
            'attempts' => $this->attempts + 1,
        ]);
    }
} 