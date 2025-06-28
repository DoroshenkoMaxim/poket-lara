@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-center">{{ __('Вход через Telegram') }}</div>

                <div class="card-body text-center">
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <p class="mb-4">Для входа в систему используйте кнопку ниже:</p>
                    
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
                        <a href="https://t.me/signallangis_bot" class="btn btn-primary btn-lg">
                            <i class="fab fa-telegram-plane"></i> Открыть бота @signallangis_bot
                        </a>
                        <p class="mt-2 text-muted">
                            <small>Если виджет не загружается, используйте прямую ссылку на бота</small>
                        </p>
                    </div>
                    
                    <div class="mt-4">
                        <small class="text-muted">
                            Если у вас нет аккаунта, сначала обратитесь к нашему боту для регистрации.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        margin-top: 50px;
    }
    
    #telegram-login-widget {
        min-height: 80px;
    }
    
    /* Стили для виджета Telegram */
    iframe[src*="oauth.telegram.org"] {
        border-radius: 10px !important;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1) !important;
    }
</style>

<script>
// Проверяем загрузку виджета Telegram через 5 секунд
setTimeout(function() {
    var widget = document.querySelector('#telegram-login-widget iframe');
    var fallback = document.getElementById('telegram-fallback');
    
    if (!widget) {
        console.log('Telegram widget не загрузился, показываем запасной вариант');
        fallback.style.display = 'block';
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
        document.getElementById('telegram-fallback').style.display = 'block';
        clearInterval(checkInterval);
    }
}, 2000);

// Обработка успешной авторизации
window.onTelegramAuth = function(user) {
    console.log('Telegram auth successful:', user);
    // Данные уже отправляются автоматически через data-auth-url
};
</script>
@endsection 