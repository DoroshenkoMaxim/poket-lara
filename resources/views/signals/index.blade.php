@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-chart-line"></i> Торговые сигналы PocketOption
                    </h4>
                    <div class="badge-group">
                        <span class="badge bg-success">
                            <i class="fas fa-check-circle"></i> Авторизован
                        </span>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-8">
                            <h5 class="text-primary">
                                <i class="fas fa-user"></i> 
                                Добро пожаловать, {{ $user->name }}
                            </h5>
                            <p class="text-muted mb-2">
                                <small>
                                    <i class="fab fa-telegram-plane"></i> Telegram ID: {{ $telegram_id }}
                                </small>
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <span class="text-success">
                                <i class="fas fa-check-circle"></i> Авторизован через Telegram
                            </span>
                        </div>
                    </div>

                    <!-- Информационные карточки -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="info-card bg-primary text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-signal fa-2x mb-2"></i>
                                    <h6>Актуальные сигналы</h6>
                                    <h4>12</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-card bg-success text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-chart-line fa-2x mb-2"></i>
                                    <h6>Точность</h6>
                                    <h4>87%</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-card bg-warning text-white">
                                <div class="card-body text-center">
                                    <i class="fas fa-clock fa-2x mb-2"></i>
                                    <h6>Обновлено</h6>
                                    <h4>5 мин</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Таблица сигналов -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th><i class="fas fa-clock"></i> Время</th>
                                    <th><i class="fas fa-coins"></i> Актив</th>
                                    <th><i class="fas fa-arrow-up"></i> Направление</th>
                                    <th><i class="fas fa-hourglass-half"></i> Экспирация</th>
                                    <th><i class="fas fa-percentage"></i> Точность</th>
                                    <th><i class="fas fa-info-circle"></i> Статус</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ now()->format('H:i') }}</td>
                                    <td><span class="badge bg-primary">EUR/USD</span></td>
                                    <td><span class="text-success"><i class="fas fa-arrow-up"></i> CALL</span></td>
                                    <td>5 минут</td>
                                    <td><span class="badge bg-success">89%</span></td>
                                    <td><span class="badge bg-success">Активен</span></td>
                                </tr>
                                <tr>
                                    <td>{{ now()->subMinutes(3)->format('H:i') }}</td>
                                    <td><span class="badge bg-primary">GBP/USD</span></td>
                                    <td><span class="text-danger"><i class="fas fa-arrow-down"></i> PUT</span></td>
                                    <td>1 минута</td>
                                    <td><span class="badge bg-warning">75%</span></td>
                                    <td><span class="badge bg-warning">Ожидание</span></td>
                                </tr>
                                <tr>
                                    <td>{{ now()->subMinutes(5)->format('H:i') }}</td>
                                    <td><span class="badge bg-primary">USD/JPY</span></td>
                                    <td><span class="text-success"><i class="fas fa-arrow-up"></i> CALL</span></td>
                                    <td>15 минут</td>
                                    <td><span class="badge bg-success">92%</span></td>
                                    <td><span class="badge bg-info">Выполнен</span></td>
                                </tr>
                                <tr>
                                    <td>{{ now()->subMinutes(8)->format('H:i') }}</td>
                                    <td><span class="badge bg-primary">AUD/USD</span></td>
                                    <td><span class="text-danger"><i class="fas fa-arrow-down"></i> PUT</span></td>
                                    <td>10 минут</td>
                                    <td><span class="badge bg-success">85%</span></td>
                                    <td><span class="badge bg-info">Выполнен</span></td>
                                </tr>
                                <tr>
                                    <td>{{ now()->subMinutes(12)->format('H:i') }}</td>
                                    <td><span class="badge bg-primary">USD/CAD</span></td>
                                    <td><span class="text-success"><i class="fas fa-arrow-up"></i> CALL</span></td>
                                    <td>3 минуты</td>
                                    <td><span class="badge bg-success">91%</span></td>
                                    <td><span class="badge bg-info">Выполнен</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Дополнительная информация -->
                    <div class="alert alert-info mt-4" role="alert">
                        <h6 class="alert-heading">
                            <i class="fas fa-info-circle"></i> Информация о сигналах
                        </h6>
                        <p class="mb-2">
                            • Сигналы обновляются каждую 1 минуту автоматически<br>
                            • Для максимальной прибыли следуйте сигналам с точностью выше 80%<br>
                            • Рекомендуемая сумма сделки: 2-5% от депозита<br>
                            • Используйте стратегию управления капиталом
                        </p>
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="fas fa-shield-alt"></i> 
                                У вас постоянный доступ к сигналам через авторизацию Telegram
                            </small>
                        </div>
                    </div>

                    <!-- Дополнительные кнопки -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <a href="https://po.cash/smart/j9IBCSAyjqdBE7" 
                               class="btn btn-success btn-lg w-100" target="_blank">
                                <i class="fas fa-external-link-alt"></i> Перейти к торговле
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="https://t.me/{{ config('services.telegram.bot_username') }}" 
                               class="btn btn-primary btn-lg w-100" target="_blank">
                                <i class="fab fa-telegram-plane"></i> Связаться с ботом
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .info-card {
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }
    
    .info-card:hover {
        transform: translateY(-5px);
    }
    
    .badge-group {
        display: flex;
        gap: 10px;
    }
    
    .table th {
        border-top: none;
        font-weight: 600;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.1);
    }
    
    .alert-heading {
        margin-bottom: 10px;
    }

    .btn-lg {
        padding: 12px 24px;
        font-size: 16px;
    }
</style>

<script>
    // Автообновление страницы каждую 1 минуту
    setTimeout(function() {
        location.reload();
    }, 60000);
    
    // Показать время до следующего обновления
    let countdown = 60;
    setInterval(function() {
        countdown--;
        if (countdown <= 0) {
            countdown = 60;
        }
        const minutes = Math.floor(countdown/60);
        const seconds = countdown % 60;
        document.title = `Сигналы (${minutes}:${seconds.toString().padStart(2, '0')})`;
    }, 1000);

    // Уведомление о новых сигналах
    document.addEventListener('DOMContentLoaded', function() {
        if (Notification.permission === 'default') {
            Notification.requestPermission();
        }
    });
</script>
@endsection 