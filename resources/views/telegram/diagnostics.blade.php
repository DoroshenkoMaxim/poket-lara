<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Диагностика Telegram бота</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fab fa-telegram-plane"></i> Диагностика Telegram бота
                        </h4>
                    </div>
                    <div class="card-body">
                        
                        <!-- Кнопки управления -->
                        <div class="row mb-4">
                            <div class="col-lg-3 col-md-6 col-sm-12 mb-2">
                                <button onclick="checkBotInfo()" class="btn btn-info w-100">
                                    <i class="fas fa-info-circle"></i> Проверить бота
                                </button>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-12 mb-2">
                                <button onclick="cleanAndSetupWebhook()" class="btn btn-danger w-100">
                                    <i class="fas fa-trash-restore"></i> Очистить и переустановить
                                </button>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-12 mb-2">
                                <button onclick="testWebhookAccess()" class="btn btn-info w-100">
                                    <i class="fas fa-network-wired"></i> Тест доступности
                                </button>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-12 mb-2">
                                <button onclick="testWebhookExternal()" class="btn btn-warning w-100">
                                    <i class="fas fa-globe"></i> Внешний тест
                                </button>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-lg-4 col-md-6 col-sm-12 mb-2">
                                <button onclick="testWebhook()" class="btn btn-secondary w-100">
                                    <i class="fas fa-vial"></i> Тест webhook endpoint
                                </button>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-12 mb-2">
                                <a href="{{ route('login') }}" class="btn btn-success w-100">
                                    <i class="fas fa-sign-in-alt"></i> Тест авторизации
                                </a>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-12 mb-2">
                                <a href="https://t.me/signallangis_bot" target="_blank" class="btn btn-primary w-100">
                                    <i class="fab fa-telegram-plane"></i> Открыть бота
                                </a>
                            </div>
                        </div>

                        <!-- Результаты -->
                        <div id="results"></div>

                        <!-- Текущие настройки -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="mb-0">Текущие настройки</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-striped">
                                    <tr>
                                        <td><strong>Домен:</strong></td>
                                        <td><code>{{ request()->getHost() }}</code></td>
                                    </tr>
                                    <tr>
                                        <td><strong>URL webhook:</strong></td>
                                        <td><code>{{ url('/telegram/webhook') }}</code></td>
                                    </tr>
                                    <tr>
                                        <td><strong>URL авторизации:</strong></td>
                                        <td><code>{{ url('/telegram/auth') }}</code></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Имя бота:</strong></td>
                                        <td><code>{{ config('services.telegram.bot_username') }}</code></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Токен бота:</strong></td>
                                        <td><code>{{ substr(config('services.telegram.bot_token'), 0, 20) }}...</code></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Инструкции -->
                        <div class="alert alert-info mt-4">
                            <h6 class="alert-heading">
                                <i class="fas fa-info-circle"></i> Инструкции по настройке
                            </h6>
                            <ol class="mb-0">
                                <li>Нажмите "Проверить бота" для получения текущей информации</li>
                                <li>Если webhook неправильный, нажмите "Переустановить webhook"</li>
                                <li>Обратитесь к <a href="https://t.me/BotFather" target="_blank">@BotFather</a> и выполните команду: 
                                    <code>/setdomain</code> → выберите бота → введите: <code>{{ request()->getHost() }}</code>
                                </li>
                                <li>Проверьте авторизацию на странице "Тест авторизации"</li>
                            </ol>
                        </div>

                        <!-- Тест виджета Telegram -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="mb-0">Тест виджета Telegram</h5>
                            </div>
                            <div class="card-body text-center">
                                <p>Тестовый виджет авторизации:</p>
                                <div id="test-widget" class="d-flex justify-content-center mb-3">
                                    <script async src="https://telegram.org/js/telegram-widget.js?22" 
                                            data-telegram-login="signallangis_bot" 
                                            data-size="large" 
                                            data-auth-url="{{ url('/telegram/auth') }}" 
                                            data-request-access="write">
                                    </script>
                                </div>
                                <small class="text-muted">Если виджет не отображается, проверьте настройки домена в BotFather</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showLoading(text = 'Загрузка...') {
            document.getElementById('results').innerHTML = `
                <div class="d-flex justify-content-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">${text}</span>
                    </div>
                    <span class="ms-2">${text}</span>
                </div>
            `;
        }

        function showResults(data, title = 'Результаты') {
            document.getElementById('results').innerHTML = `
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">${title}</h5>
                    </div>
                    <div class="card-body">
                        <pre class="bg-light p-3" style="max-height: 400px; overflow-y: auto;">${JSON.stringify(data, null, 2)}</pre>
                    </div>
                </div>
            `;
        }

        function showError(error, title = 'Ошибка') {
            document.getElementById('results').innerHTML = `
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">${title}</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-danger">${error}</div>
                    </div>
                </div>
            `;
        }

        async function checkBotInfo() {
            showLoading('Проверяем информацию о боте...');
            try {
                const response = await fetch('/telegram/info');
                const data = await response.json();
                showResults(data, 'Информация о боте');
            } catch (error) {
                showError('Ошибка при получении информации о боте: ' + error.message);
            }
        }

        async function reinstallWebhook() {
            showLoading('Переустанавливаем webhook...');
            try {
                const response = await fetch('/telegram/reinstall');
                const data = await response.json();
                showResults(data, 'Webhook переустановлен');
            } catch (error) {
                showError('Ошибка при переустановке webhook: ' + error.message);
            }
        }

        async function cleanAndSetupWebhook() {
            showLoading('Полная очистка и переустановка webhook...');
            try {
                const response = await fetch('/telegram/clean-setup');
                const data = await response.json();
                showResults(data, 'Webhook полностью переустановлен');
            } catch (error) {
                showError('Ошибка при полной переустановке webhook: ' + error.message);
            }
        }

        async function testWebhook() {
            showLoading('Тестируем webhook endpoint...');
            try {
                const response = await fetch('/telegram/test-webhook');
                const data = await response.json();
                showResults(data, 'Тест webhook endpoint');
            } catch (error) {
                showError('Ошибка при тестировании webhook: ' + error.message);
            }
        }

        async function testWebhookAccess() {
            showLoading('Проверяем доступность webhook endpoint...');
            try {
                const response = await fetch('/telegram/webhook-test');
                const data = await response.json();
                
                if (response.ok) {
                    showResults({
                        ...data,
                        message: 'Webhook endpoint доступен!',
                        next_step: 'Если endpoint доступен, но бот не отвечает, проблема в настройке webhook в Telegram'
                    }, 'Тест доступности webhook');
                } else {
                    showError('Webhook endpoint недоступен: ' + response.status);
                }
            } catch (error) {
                showError('Ошибка при тестировании доступности: ' + error.message);
            }
        }

        async function testWebhookExternal() {
            showLoading('Выполняем внешний тест webhook (отправляем POST запрос)...');
            try {
                const response = await fetch('/telegram/test-webhook-external');
                const data = await response.json();
                showResults(data, 'Результат внешнего тестирования webhook');
            } catch (error) {
                showError('Ошибка при внешнем тестировании: ' + error.message);
            }
        }

        // Автоматически проверяем информацию при загрузке страницы
        window.onload = function() {
            checkBotInfo();
        };
    </script>
</body>
</html> 