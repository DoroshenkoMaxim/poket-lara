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
                        @if($access_data['method'] === 'token')
                            <span class="badge bg-success">
                                <i class="fas fa-clock"></i> 
                                Доступ до {{ $access_data['expires_at']->format('d.m.Y H:i') }}
                            </span>
                        @else
                            <span class="badge bg-info">
                                <i class="fas fa-key"></i> Постоянный доступ
                            </span>
                        @endif
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
                                Telegram ID: {{ $access_data['telegram_id'] }}
                            </h5>
                            <p class="text-muted mb-2">
                                <small>
                                    Click ID: {{ $access_data['click_id'] ?? 'N/A' }} | 
                                    Trader ID: {{ $access_data['trader_id'] ?? 'N/A' }}
                                </small>
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            @if(!Auth::check())
                                <a href="{{ $telegram_login_url }}" class="btn btn-primary">
                                    <i class="fab fa-telegram-plane"></i> Войти через Telegram
                                </a>
                            @else
                                <span class="text-success">
                                    <i class="fas fa-check-circle"></i> Авторизован как {{ Auth::user()->name }}
                                </span>
                            @endif
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
                            </tbody>
                        </table>
                    </div>

                    <!-- Дополнительная информация -->
                    <div class="alert alert-info mt-4" role="alert">
                        <h6 class="alert-heading">
                            <i class="fas fa-info-circle"></i> Информация
                        </h6>
                        <p class="mb-0">
                            Сигналы обновляются каждые 5 минут. Для получения максимальной прибыли рекомендуется следовать сигналам с точностью выше 80%.
                            @if($access_data['method'] === 'token')
                                Ваш временный доступ истекает {{ $access_data['expires_at']->format('d.m.Y в H:i') }}. 
                                После этого вы сможете авторизоваться через виджет Telegram.
                            @endif
                        </p>
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
</style>

<script>
    // Автообновление страницы каждые 5 минут
    setTimeout(function() {
        location.reload();
    }, 300000);
    
    // Показать время до следующего обновления
    let countdown = 300;
    setInterval(function() {
        countdown--;
        if (countdown <= 0) {
            countdown = 300;
        }
        document.title = `Сигналы (обновление через ${Math.floor(countdown/60)}:${(countdown%60).toString().padStart(2, '0')})`;
    }, 1000);
</script>
@endsection 