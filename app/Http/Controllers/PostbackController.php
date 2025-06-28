<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Services\AffiliateService;

class PostbackController extends Controller
{
    protected AffiliateService $affiliateService;

    public function __construct(AffiliateService $affiliateService)
    {
        $this->affiliateService = $affiliateService;
    }

    /**
     * Обработать постбек от PocketOption
     */
    public function handlePostback(Request $request): JsonResponse
    {
        try {
            // Логируем входящий запрос
            $logData = [
                'method' => $request->method(),
                'headers' => $request->headers->all(),
                'get' => $request->query->all(),
                'post' => $request->all(),
                'query_string' => $request->getQueryString(),
                'timestamp' => now()->toDateTimeString()
            ];
            
            Log::info('Postback received', $logData);
            
            // Получаем данные из GET запроса
            $data = [
                'click_id' => $request->get('click_id'),
                'trader_id' => $request->get('trader_id'),
                'country' => $request->get('country'),
                'promo' => $request->get('promo') ?? $request->get('code'),
                'device_type' => $request->get('device_type'),
                'os_version' => $request->get('os_version'),
                'browser' => $request->get('browser'),
                'link_type' => $request->get('link_type'),
                'date_time' => $request->get('date_time'),
                'site_id' => $request->get('site_id'),
                'sub_id1' => $request->get('sub_id1'),
                'cid' => $request->get('cid'),
            ];

            $event = $request->get('event', 'reg'); // По умолчанию регистрация

            // Проверяем обязательные параметры
            if (!$data['click_id'] || !$data['trader_id']) {
                return response()->json([
                    'error' => 'click_id and trader_id are required'
                ], 400);
            }

            // Проверяем, что это событие регистрации
            if ($event !== 'reg') {
                return response()->json([
                    'success' => true,
                    'message' => 'Event received but not processed'
                ]);
            }

            // Обрабатываем постбек
            $result = $this->affiliateService->processPostback($data);
            
            // Отправляем уведомление пользователю
            $notificationSent = $this->affiliateService->sendRegistrationNotification($result);
            
            // Создаем авторизованную ссылку для Laravel
            $authLink = $this->affiliateService->createAuthenticatedLink($result['telegram_id']);

            Log::info('Postback processed successfully', [
                'click_id' => $data['click_id'],
                'trader_id' => $data['trader_id'],
                'telegram_id' => $result['telegram_id'],
                'notification_sent' => $notificationSent,
                'auth_link' => $authLink
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Registration processed successfully',
                'click_id' => $data['click_id'],
                'trader_id' => $data['trader_id'],
                'signals_url' => $result['signals_url'],
                'auth_link' => $authLink,
                'notification_sent' => $notificationSent
            ]);

        } catch (\InvalidArgumentException $e) {
            Log::warning('Invalid postback data', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);
            
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
            
        } catch (\Exception $e) {
            Log::error('Postback processing error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $request->all()
            ]);
            
            if ($e->getMessage() === 'click_id not found in our system') {
                return response()->json([
                    'error' => 'click_id not found in our system'
                ], 404);
            }
            
            if ($e->getMessage() === 'Registration already processed') {
                return response()->json([
                    'success' => true,
                    'message' => 'Registration already processed'
                ]);
            }
            
            return response()->json([
                'error' => 'Internal server error: ' . $e->getMessage()
            ], 500);
        }
    }
} 