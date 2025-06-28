@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-center">
                    <i class="fab fa-telegram-plane"></i> 
                    {{ __('Вход через Telegram') }}
                </div>

                <div class="card-body text-center">
                    @if (session('show_bot_button'))
                        <!-- Пользователь не зарегистрирован - показываем кнопку бота -->
                        <div class="alert alert-warning" role="alert">
                            <h5 class="alert-heading">
                                <i class="fas fa-exclamation-triangle"></i> 
                                Доступ ограничен
                            </h5>
                            <p class="mb-3">
                                Пользователь <strong>{{ session('telegram_name') }}</strong> 
                                не найден в нашей системе.
                            </p>
                            <p class="mb-0">
                                Для получения доступа к торговым сигналам сначала зарегистрируйтесь через нашего бота.
                            </p>
                        </div>

                        <div class="registration-block">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-robot"></i> 
                                Шаги для регистрации:
                            </h5>
                            
                            <div class="steps-list text-start mb-4">
                                <div class="step-item">
                                    <span class="step-number">1</span>
                                    <span class="step-text">Откройте нашего бота</span>
                                </div>
                                <div class="step-item">
                                    <span class="step-number">2</span>
                                    <span class="step-text">Получите реферальную ссылку</span>
                                </div>
                                <div class="step-item">
                                    <span class="step-number">3</span>
                                    <span class="step-text">Зарегистрируйтесь на PocketOption</span>
                                </div>
                                <div class="step-item">
                                    <span class="step-number">4</span>
                                    <span class="step-text">После депозита больше 10$ получите доступ к сигналам</span>
                                </div>
                            </div>

                            <a href="https://t.me/signallangis_bot" 
                               class="btn btn-primary btn-lg mb-3" target="_blank">
                                <i class="fab fa-telegram-plane"></i> 
                                Открыть бота @signallangis_bot
                            </a>
                            
                            <div class="mt-3">
                                <a href="{{ route('signals') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left"></i> 
                                    Вернуться к сигналам
                                </a>
                            </div>
                        </div>

                    @else
                        <!-- Обычная авторизация через виджет -->
                        @if (session('error'))
                            <div class="alert alert-danger" role="alert">
                                <i class="fas fa-times-circle"></i>
                                {{ session('error') }}
                            </div>
                        @endif

                        <div class="auth-welcome">
                            <h5 class="text-primary mb-3">
                                <i class="fas fa-sign-in-alt"></i>
                                Авторизация в системе
                            </h5>
                            <p class="mb-4 text-muted">
                                Для входа в систему торговых сигналов используйте кнопку авторизации Telegram:
                            </p>
                        </div>
                        
                        <div id="telegram-login-widget" class="d-flex justify-content-center mb-3">
                            <!-- Telegram Login Widget будет загружен здесь -->
                            <script async src="https://telegram.org/js/telegram-widget.js?22" 
                                    data-telegram-login="signallangis_bot" 
                                    data-size="large" 
                                    data-auth-url="{{ route('telegram.auth') }}" 
                                    data-request-access="write">
                            </script>
                        </div>
                        
                        <!-- Запасной вариант если виджет не загрузится -->
                        <div id="telegram-fallback" style="display: none;" class="text-center">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                Виджет не загружается. Попробуйте обновить страницу или использовать прямую ссылку:
                            </div>
                            <a href="https://t.me/signallangis_bot" 
                               class="btn btn-primary btn-lg">
                                <i class="fab fa-telegram-plane"></i> 
                                Открыть бота @signallangis_bot
                            </a>
                        </div>
                        
                        <div class="mt-4">
                            <small class="text-muted">
                                <i class="fas fa-question-circle"></i>
                                Если у вас нет доступа, сначала зарегистрируйтесь через нашего бота
                            </small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        margin-top: 30px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border: none;
        border-radius: 10px;
    }
    
    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-weight: 600;
        border-radius: 10px 10px 0 0 !important;
    }
    
    #telegram-login-widget {
        min-height: 80px;
    }
    
    /* Стили для виджета Telegram */
    iframe[src*="oauth.telegram.org"] {
        border-radius: 10px !important;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1) !important;
    }

    /* Стили для шагов регистрации */
    .steps-list {
        max-width: 400px;
        margin: 0 auto;
    }

    .step-item {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        padding: 10px;
        background-color: #f8f9fa;
        border-radius: 8px;
        border-left: 4px solid #007bff;
    }

    .step-number {
        background-color: #007bff;
        color: white;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        margin-right: 15px;
        flex-shrink: 0;
    }

    .step-text {
        font-size: 14px;
        line-height: 1.4;
    }

    .registration-block {
        padding: 20px;
        background-color: #f8f9fa;
        border-radius: 10px;
        margin-top: 20px;
    }

    .auth-welcome {
        padding: 20px;
        background-color: #f8f9fa;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .btn-lg {
        padding: 12px 30px;
        font-size: 16px;
        border-radius: 8px;
    }
</style>

<script>
@if (!session('show_bot_button'))
// Проверяем загрузку виджета Telegram только если показываем виджет
setTimeout(function() {
    var widget = document.querySelector('#telegram-login-widget iframe');
    var fallback = document.getElementById('telegram-fallback');
    
    if (!widget) {
        console.log('Telegram widget не загрузился, показываем запасной вариант');
        if (fallback) fallback.style.display = 'block';
    } else {
        console.log('Telegram widget успешно загружен');
    }
}, 5000);

// Также проверяем каждые 2 секунды в течение первых 10 секунд
var checkCount = 0;
var checkInterval = setInterval(function() {
    checkCount++;
    var widget = document.querySelector('#telegram-login-widget iframe');
    
    if (widget) {
        console.log('Telegram widget найден на проверке #' + checkCount);
        clearInterval(checkInterval);
    } else if (checkCount >= 5) { // 5 проверок = 10 секунд
        console.log('Telegram widget не найден после 5 проверок, показываем запасной вариант');
        var fallback = document.getElementById('telegram-fallback');
        if (fallback) fallback.style.display = 'block';
        clearInterval(checkInterval);
    }
}, 2000);

// Обработка успешной авторизации
window.onTelegramAuth = function(user) {
    console.log('Telegram auth successful:', user);
    // Данные уже отправляются автоматически через data-auth-url
};
@endif
</script>
@endsection 