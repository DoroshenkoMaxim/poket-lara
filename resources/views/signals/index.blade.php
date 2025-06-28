@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- Информация о пользователе -->
            <div class="card mb-4 user-info-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="text-primary mb-1">
                                <i class="fas fa-user-circle"></i> 
                                Добро пожаловать, {{ $user->name }}
                            </h5>
                            <p class="text-muted mb-0">
                                <i class="fab fa-telegram-plane"></i> 
                                Telegram ID: {{ $telegram_id }}
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <span class="badge bg-success fs-6">
                                <i class="fas fa-check-circle"></i> Авторизован
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Основной интерфейс -->
            <div class="card main-card">
                <div class="card-header text-center">
                    <h3 class="mb-0">
                        <i class="fas fa-chart-line text-primary"></i>
                        Торговые сигналы PocketOption
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Фильтры -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <button type="button" class="btn btn-outline-primary btn-lg w-100 filter-btn" 
                                    data-bs-toggle="modal" data-bs-target="#currencyModal" id="currencyBtn">
                                <i class="fas fa-coins"></i>
                                <span class="filter-title">Валюты</span>
                                <span class="filter-stats">(0% Win)</span>
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-outline-info btn-lg w-100 filter-btn" 
                                    data-bs-toggle="modal" data-bs-target="#timeframeModal" id="timeframeBtn">
                                <i class="fas fa-clock"></i>
                                <span class="filter-title">Таймфреймы</span>
                                <span class="filter-stats">(0% Win)</span>
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-outline-warning btn-lg w-100 filter-btn" 
                                    id="martingaleBtn">
                                <i class="fas fa-chart-area"></i>
                                <span class="filter-title">Весь рынок</span>
                                <span class="filter-subtitle">Мартингейл</span>
                                <span class="filter-stats">(0% Win)</span>
                            </button>
                        </div>
                    </div>

                    <!-- Кнопка поиска сигнала -->
                    <div class="text-center mb-4">
                        <button type="button" class="btn btn-success btn-lg px-5 py-3" id="findSignalBtn">
                            <i class="fas fa-search"></i>
                            <span class="btn-text">Найти сигнал</span>
                        </button>
                    </div>

                    <!-- Анимация загрузки -->
                    <div class="loading-animation d-none" id="loadingAnimation">
                        <div class="spinner-container">
                            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;"></div>
                            <p class="mt-3 text-muted">Поиск сигнала...</p>
                        </div>
                    </div>

                    <!-- Результат сигнала -->
                    <div class="signal-result d-none" id="signalResult">
                        <div class="signal-card">
                            <div class="signal-direction">
                                <div class="direction-icon">
                                    <i class="fas fa-arrow-up"></i>
                                </div>
                                <div class="direction-text">CALL</div>
                            </div>
                            <div class="signal-info">
                                <div class="signal-currency">EUR/USD</div>
                                <div class="signal-details">
                                    <div class="detail-item">
                                        <span class="detail-label">Время:</span>
                                        <span class="detail-value signal-time">5 минут</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Вероятность:</span>
                                        <span class="detail-value signal-probability">87%</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Цена входа:</span>
                                        <span class="detail-value signal-entry-price">1.0856</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Результат сделки -->
                        <div class="trade-result d-none" id="tradeResult">
                            <div class="result-badge">
                                <i class="fas fa-trophy"></i>
                                <span class="result-text">WIN</span>
                            </div>
                        </div>
                    </div>

                    <!-- Кнопки действий -->
                    <div class="row mt-5">
                        <div class="col-md-6">
                            <a href="https://po.cash/smart/j9IBCSAyjqdBE7" 
                               class="btn btn-success btn-lg w-100 action-btn" target="_blank">
                                <i class="fas fa-chart-line"></i>
                                <span class="btn-title">Перейти к торговле</span>
                                <small class="btn-subtitle">PocketOption</small>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="https://t.me/{{ config('services.telegram.bot_username', 'signallangis_bot') }}" 
                               class="btn btn-primary btn-lg w-100 action-btn" target="_blank">
                                <i class="fab fa-telegram-plane"></i>
                                <span class="btn-title">Связаться с ботом</span>
                                <small class="btn-subtitle">Поддержка и помощь</small>
                            </a>
                        </div>
                    </div>

                    <!-- Информационный блок -->
                    <div class="alert alert-info mt-4 info-block" role="alert">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h6 class="alert-heading mb-2">
                                    <i class="fas fa-lightbulb"></i> Советы по торговле
                                </h6>
                                <ul class="mb-2 tips-list">
                                    <li>Используйте сигналы с вероятностью выше 80%</li>
                                    <li>Рекомендуемая сумма сделки: 2-5% от депозита</li>
                                    <li>В режиме мартингейл увеличивайте ставку в 2.2 раза</li>
                                    <li>Следите за экономическими новостями</li>
                                </ul>
                            </div>
                            <div class="col-md-4 text-center">
                                <div class="stats-card">
                                    <div class="stats-value" id="totalSignals">0</div>
                                    <div class="stats-label">Сигналов сегодня</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно валют -->
<div class="modal fade" id="currencyModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-coins"></i> Выберите валютную пару
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-primary w-100 currency-btn" data-currency="EUR/USD">
                            <strong>EUR/USD</strong>
                            <small class="d-block text-muted">Euro/Dollar</small>
                            <span class="stats-badge">0% Win</span>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-primary w-100 currency-btn" data-currency="GBP/USD">
                            <strong>GBP/USD</strong>
                            <small class="d-block text-muted">Pound/Dollar</small>
                            <span class="stats-badge">0% Win</span>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-primary w-100 currency-btn" data-currency="USD/JPY">
                            <strong>USD/JPY</strong>
                            <small class="d-block text-muted">Dollar/Yen</small>
                            <span class="stats-badge">0% Win</span>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-primary w-100 currency-btn" data-currency="AUD/USD">
                            <strong>AUD/USD</strong>
                            <small class="d-block text-muted">Aussie/Dollar</small>
                            <span class="stats-badge">0% Win</span>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-primary w-100 currency-btn" data-currency="USD/CAD">
                            <strong>USD/CAD</strong>
                            <small class="d-block text-muted">Dollar/Canadian</small>
                            <span class="stats-badge">0% Win</span>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-primary w-100 currency-btn" data-currency="EUR/GBP">
                            <strong>EUR/GBP</strong>
                            <small class="d-block text-muted">Euro/Pound</small>
                            <span class="stats-badge">0% Win</span>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-primary w-100 currency-btn" data-currency="USD/CHF">
                            <strong>USD/CHF</strong>
                            <small class="d-block text-muted">Dollar/Franc</small>
                            <span class="stats-badge">0% Win</span>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-primary w-100 currency-btn" data-currency="NZD/USD">
                            <strong>NZD/USD</strong>
                            <small class="d-block text-muted">Kiwi/Dollar</small>
                            <span class="stats-badge">0% Win</span>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-primary w-100 currency-btn" data-currency="EUR/JPY">
                            <strong>EUR/JPY</strong>
                            <small class="d-block text-muted">Euro/Yen</small>
                            <span class="stats-badge">0% Win</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно таймфреймов -->
<div class="modal fade" id="timeframeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-clock"></i> Выберите таймфрейм
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <button class="btn btn-outline-info w-100 timeframe-btn" data-timeframe="5s">
                            <strong>5 секунд</strong>
                            <span class="stats-badge">0% Win</span>
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-outline-info w-100 timeframe-btn" data-timeframe="15s">
                            <strong>15 секунд</strong>
                            <span class="stats-badge">0% Win</span>
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-outline-info w-100 timeframe-btn" data-timeframe="30s">
                            <strong>30 секунд</strong>
                            <span class="stats-badge">0% Win</span>
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-outline-info w-100 timeframe-btn" data-timeframe="1m">
                            <strong>1 минута</strong>
                            <span class="stats-badge">0% Win</span>
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-outline-info w-100 timeframe-btn" data-timeframe="2m">
                            <strong>2 минуты</strong>
                            <span class="stats-badge">0% Win</span>
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-outline-info w-100 timeframe-btn" data-timeframe="5m">
                            <strong>5 минут</strong>
                            <span class="stats-badge">0% Win</span>
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-outline-info w-100 timeframe-btn" data-timeframe="15m">
                            <strong>15 минут</strong>
                            <span class="stats-badge">0% Win</span>
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-outline-info w-100 timeframe-btn" data-timeframe="30m">
                            <strong>30 минут</strong>
                            <span class="stats-badge">0% Win</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Стили -->
<style>
    .user-info-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }

    .main-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    .filter-btn {
        height: 100px;
        border-radius: 15px;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .filter-btn:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }

    .filter-btn.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-color: transparent;
    }

    .filter-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin: 0;
    }

    .filter-subtitle {
        font-size: 0.9rem;
        opacity: 0.8;
        margin: 0;
    }

    .filter-stats {
        font-size: 0.8rem;
        margin-top: 5px;
        opacity: 0.7;
    }

    #findSignalBtn {
        border-radius: 25px;
        background: linear-gradient(135deg, #00c851 0%, #00a040 100%);
        border: none;
        font-size: 1.2rem;
        font-weight: 600;
        box-shadow: 0 5px 15px rgba(0,200,81,0.3);
        transition: all 0.3s ease;
    }

    #findSignalBtn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,200,81,0.4);
    }

    .loading-animation {
        text-align: center;
        padding: 50px 0;
    }

    .spinner-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .signal-result {
        max-width: 600px;
        margin: 0 auto;
    }

    .signal-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        padding: 30px;
        color: white;
        display: flex;
        align-items: center;
        gap: 30px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.2);
    }

    .signal-direction {
        text-align: center;
        flex-shrink: 0;
    }

    .direction-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: rgba(255,255,255,0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 10px;
        animation: pulse 2s infinite;
    }

    .direction-icon i {
        font-size: 2.5rem;
    }

    .direction-icon.call {
        background: rgba(0,200,81,0.3);
        color: #00c851;
    }

    .direction-icon.put {
        background: rgba(255,61,0,0.3);
        color: #ff3d00;
    }

    .direction-text {
        font-size: 1.2rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .signal-info {
        flex-grow: 1;
    }

    .signal-currency {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 15px;
    }

    .signal-details {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .detail-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }

    .detail-label {
        font-size: 0.9rem;
        opacity: 0.8;
    }

    .detail-value {
        font-size: 1.1rem;
        font-weight: 600;
    }

    .trade-result {
        margin-top: 20px;
        text-align: center;
    }

    .result-badge {
        display: inline-block;
        padding: 15px 30px;
        border-radius: 25px;
        font-size: 1.3rem;
        font-weight: 700;
        text-transform: uppercase;
        animation: bounce 1s ease-in-out;
    }

    .result-badge.win {
        background: linear-gradient(135deg, #00c851 0%, #00a040 100%);
        color: white;
        box-shadow: 0 5px 15px rgba(0,200,81,0.3);
    }

    .result-badge.lose {
        background: linear-gradient(135deg, #ff3d00 0%, #cc0000 100%);
        color: white;
        box-shadow: 0 5px 15px rgba(255,61,0,0.3);
    }

    .currency-btn, .timeframe-btn {
        height: 80px;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .currency-btn:hover, .timeframe-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.15);
    }

    .stats-badge {
        position: absolute;
        top: 5px;
        right: 5px;
        background: rgba(0,0,0,0.1);
        padding: 2px 6px;
        border-radius: 10px;
        font-size: 0.7rem;
        font-weight: 600;
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }

    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
        40% { transform: translateY(-10px); }
        60% { transform: translateY(-5px); }
    }

    .fade-in {
        animation: fadeIn 0.5s ease-in;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .zoom-in {
        animation: zoomIn 0.5s ease-out;
    }

    @keyframes zoomIn {
        from { opacity: 0; transform: scale(0.8); }
        to { opacity: 1; transform: scale(1); }
    }

    /* Кнопки действий */
    .action-btn {
        height: 80px;
        border-radius: 15px;
        position: relative;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        background: linear-gradient(135deg, #00c851 0%, #00a040 100%);
        border: none;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }

    .action-btn.btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .action-btn:hover {
        transform: translateY(-5px) scale(1.02);
        box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        text-decoration: none;
        color: white;
    }

    .action-btn i {
        font-size: 1.5rem;
        margin-bottom: 5px;
    }

    .btn-title {
        font-size: 1.1rem;
        font-weight: 700;
        display: block;
        line-height: 1.2;
    }

    .btn-subtitle {
        font-size: 0.8rem;
        opacity: 0.8;
        display: block;
        line-height: 1;
    }

    /* Информационный блок */
    .info-block {
        border: none;
        border-radius: 15px;
        background: linear-gradient(135deg, rgba(38, 198, 218, 0.1) 0%, rgba(0, 172, 193, 0.1) 100%);
        border-left: 4px solid #26c6da;
        backdrop-filter: blur(10px);
    }

    .tips-list {
        list-style: none;
        padding-left: 0;
    }

    .tips-list li {
        padding: 5px 0;
        position: relative;
        padding-left: 20px;
    }

    .tips-list li::before {
        content: "•";
        color: #26c6da;
        font-weight: bold;
        position: absolute;
        left: 0;
    }

    .stats-card {
        background: linear-gradient(135deg, #26c6da 0%, #00acc1 100%);
        color: white;
        padding: 20px;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(38, 198, 218, 0.3);
    }

    .stats-value {
        font-size: 2.5rem;
        font-weight: 900;
        line-height: 1;
        margin-bottom: 5px;
    }

    .stats-label {
        font-size: 0.9rem;
        opacity: 0.9;
        font-weight: 600;
    }

    /* Адаптивность для мобильных устройств */
    @media (max-width: 768px) {
        .action-btn {
            height: 70px;
            margin-bottom: 15px;
        }
        
        .btn-title {
            font-size: 1rem;
        }
        
        .stats-card {
            margin-top: 20px;
        }
        
        .stats-value {
            font-size: 2rem;
        }
    }
</style>

<!-- JavaScript -->
<script>
    class SignalGenerator {
        constructor() {
            this.currencies = [
                'EUR/USD', 'GBP/USD', 'USD/JPY', 'AUD/USD', 'USD/CAD', 
                'EUR/GBP', 'USD/CHF', 'NZD/USD', 'EUR/JPY'
            ];
            this.timeframes = ['5s', '15s', '30s', '1m', '2m', '5m', '15m', '30m'];
            this.selectedCurrency = null;
            this.selectedTimeframe = null;
            this.isMartingale = false;
            this.lastSignal = null;
            this.stats = this.loadStats();
            this.init();
        }

        init() {
            this.bindEvents();
            this.updateStatsDisplay();
        }

        bindEvents() {
            // Фильтры
            document.getElementById('martingaleBtn').addEventListener('click', () => {
                this.toggleMartingale();
            });

            // Выбор валюты
            document.querySelectorAll('.currency-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    this.selectCurrency(btn.dataset.currency);
                    bootstrap.Modal.getInstance(document.getElementById('currencyModal')).hide();
                });
            });

            // Выбор таймфрейма
            document.querySelectorAll('.timeframe-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    this.selectTimeframe(btn.dataset.timeframe);
                    bootstrap.Modal.getInstance(document.getElementById('timeframeModal')).hide();
                });
            });

            // Поиск сигнала
            document.getElementById('findSignalBtn').addEventListener('click', () => {
                this.findSignal();
            });
        }

        toggleMartingale() {
            this.isMartingale = !this.isMartingale;
            const btn = document.getElementById('martingaleBtn');
            
            if (this.isMartingale) {
                btn.classList.add('active');
                this.selectedCurrency = null;
                this.selectedTimeframe = null;
                this.updateFilterButtons();
                this.showToast('⚡ Режим мартингейла активирован', 'warning');
            } else {
                btn.classList.remove('active');
                this.showToast('ℹ️ Режим мартингейла отключен', 'info');
            }
            
            this.playSound('notification');
        }

        selectCurrency(currency) {
            this.selectedCurrency = currency;
            this.isMartingale = false;
            this.updateFilterButtons();
            this.showToast(`💱 Выбрана валютная пара: ${currency}`, 'success');
            this.playSound('notification');
        }

        selectTimeframe(timeframe) {
            this.selectedTimeframe = timeframe;
            this.isMartingale = false;
            this.updateFilterButtons();
            this.showToast(`⏱️ Выбран таймфрейм: ${timeframe}`, 'success');
            this.playSound('notification');
        }

        updateFilterButtons() {
            const currencyBtn = document.getElementById('currencyBtn');
            const timeframeBtn = document.getElementById('timeframeBtn');
            const martingaleBtn = document.getElementById('martingaleBtn');

            // Сброс всех активных состояний
            currencyBtn.classList.remove('active');
            timeframeBtn.classList.remove('active');
            martingaleBtn.classList.remove('active');

            if (this.selectedCurrency) {
                currencyBtn.classList.add('active');
                currencyBtn.querySelector('.filter-title').textContent = this.selectedCurrency;
            } else {
                currencyBtn.querySelector('.filter-title').textContent = 'Валюты';
            }

            if (this.selectedTimeframe) {
                timeframeBtn.classList.add('active');
                timeframeBtn.querySelector('.filter-title').textContent = this.selectedTimeframe;
            } else {
                timeframeBtn.querySelector('.filter-title').textContent = 'Таймфреймы';
            }

            if (this.isMartingale) {
                martingaleBtn.classList.add('active');
            }
        }

        async findSignal() {
            const findBtn = document.getElementById('findSignalBtn');
            const loadingAnimation = document.getElementById('loadingAnimation');
            const signalResult = document.getElementById('signalResult');
            const tradeResult = document.getElementById('tradeResult');

            // Показать загрузку с улучшенной анимацией
            findBtn.disabled = true;
            findBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span class="btn-text">Поиск сигнала...</span>';
            signalResult.classList.add('d-none');
            tradeResult.classList.add('d-none');
            loadingAnimation.classList.remove('d-none');
            
            // Звук начала поиска
            this.playSound('start');
            this.showToast('🔍 Поиск сигнала...', 'info');
            
            // Показать сообщения поиска
            this.showSearchMessages();

            // Симуляция сложного поиска
            await this.delay(3000);

            // Сгенерировать сигнал
            const signal = this.generateSignal();
            this.displaySignal(signal);

            // Скрыть загрузку и показать результат
            loadingAnimation.classList.add('d-none');
            signalResult.classList.remove('d-none');
            signalResult.classList.add('fade-in');

            this.showToast('✅ Сигнал найден!', 'success');
            this.playSound('notification');

            // Ждем завершения торговли с индикатором прогресса
            const duration = this.getTimeframeDuration(signal.timeframe);
            await this.waitForTradeCompletion(duration, signal);

            // Определить результат и показать
            const isWin = Math.random() > 0.25; // 75% шанс выигрыша
            this.showTradeResult(isWin, signal);

            // Обновить статистику
            this.updateStats(signal, isWin);
            this.updateStatsDisplay();

            // Сохранить сигнал для мартингейла
            this.lastSignal = { ...signal, result: isWin };

            // Вернуть кнопку в исходное состояние
            findBtn.disabled = false;
            findBtn.innerHTML = '<i class="fas fa-search"></i> <span class="btn-text">Найти сигнал</span>';
        }

        showSearchMessages() {
            const messages = [
                'Анализ рынка...',
                'Поиск паттернов...',
                'Вычисление вероятностей...',
                'Подготовка сигнала...'
            ];

            const loadingText = document.querySelector('.loading-animation p');
            let messageIndex = 0;

            const interval = setInterval(() => {
                if (messageIndex < messages.length) {
                    loadingText.textContent = messages[messageIndex];
                    messageIndex++;
                } else {
                    clearInterval(interval);
                }
            }, 750);
        }

        async waitForTradeCompletion(duration, signal) {
            const progressBar = this.createProgressBar();
            const countdown = this.createCountdown(duration);
            
            return new Promise(resolve => {
                let elapsed = 0;
                const interval = setInterval(() => {
                    elapsed += 100;
                    const progress = (elapsed / duration) * 100;
                    
                    if (progressBar) {
                        progressBar.style.width = progress + '%';
                    }
                    
                    if (countdown) {
                        const remaining = Math.ceil((duration - elapsed) / 1000);
                        countdown.textContent = `${remaining}s`;
                    }
                    
                    if (elapsed >= duration) {
                        clearInterval(interval);
                        if (progressBar) progressBar.parentElement.remove();
                        if (countdown) countdown.remove();
                        resolve();
                    }
                }, 100);
            });
        }

        createProgressBar() {
            const signalCard = document.querySelector('.signal-card');
            if (!signalCard) return null;

            const progressContainer = document.createElement('div');
            progressContainer.className = 'progress-container';
            progressContainer.style.cssText = `
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                height: 4px;
                background: rgba(255,255,255,0.2);
                border-radius: 0 0 25px 25px;
                overflow: hidden;
            `;

            const progressBar = document.createElement('div');
            progressBar.className = 'progress-bar';
            progressBar.style.cssText = `
                height: 100%;
                width: 0%;
                background: linear-gradient(90deg, #00c851, #00ff66);
                transition: width 0.1s linear;
                box-shadow: 0 0 10px rgba(0,255,102,0.5);
            `;

            progressContainer.appendChild(progressBar);
            signalCard.appendChild(progressContainer);

            return progressBar;
        }

        createCountdown(duration) {
            const signalCard = document.querySelector('.signal-card');
            if (!signalCard) return null;

            const countdown = document.createElement('div');
            countdown.className = 'countdown';
            countdown.style.cssText = `
                position: absolute;
                top: 15px;
                right: 15px;
                background: rgba(0,0,0,0.3);
                color: white;
                padding: 5px 10px;
                border-radius: 15px;
                font-weight: 600;
                font-size: 0.9rem;
                backdrop-filter: blur(5px);
                border: 1px solid rgba(255,255,255,0.2);
            `;

            signalCard.appendChild(countdown);
            return countdown;
        }

        generateSignal() {
            let currency, timeframe, direction;

            if (this.isMartingale && this.lastSignal && !this.lastSignal.result) {
                // Мартингейл: та же валюта, то же направление, меньший таймфрейм
                currency = this.lastSignal.currency;
                direction = this.lastSignal.direction;
                timeframe = this.getNextSmallerTimeframe(this.lastSignal.timeframe);
            } else {
                // Обычная логика
                currency = this.selectedCurrency || this.currencies[Math.floor(Math.random() * this.currencies.length)];
                timeframe = this.selectedTimeframe || this.timeframes[Math.floor(Math.random() * this.timeframes.length)];
                direction = Math.random() > 0.5 ? 'CALL' : 'PUT';
            }

            return {
                currency,
                timeframe,
                direction,
                probability: Math.floor(Math.random() * 30) + 70, // 70-99%
                entryPrice: this.generatePrice(currency),
                timestamp: new Date()
            };
        }

        getNextSmallerTimeframe(currentTimeframe) {
            const index = this.timeframes.indexOf(currentTimeframe);
            return index > 0 ? this.timeframes[index - 1] : this.timeframes[0];
        }

        generatePrice(currency) {
            const prices = {
                'EUR/USD': 1.0800 + Math.random() * 0.0200,
                'GBP/USD': 1.2500 + Math.random() * 0.0300,
                'USD/JPY': 145.00 + Math.random() * 5.00,
                'AUD/USD': 0.6600 + Math.random() * 0.0200,
                'USD/CAD': 1.3500 + Math.random() * 0.0200,
                'EUR/GBP': 0.8600 + Math.random() * 0.0200,
                'USD/CHF': 0.9100 + Math.random() * 0.0200,
                'NZD/USD': 0.6100 + Math.random() * 0.0200,
                'EUR/JPY': 156.00 + Math.random() * 4.00
            };
            return prices[currency]?.toFixed(4) || '1.0000';
        }

        displaySignal(signal) {
            const directionIcon = document.querySelector('.direction-icon');
            const directionText = document.querySelector('.direction-text');
            const signalCurrency = document.querySelector('.signal-currency');
            const signalTime = document.querySelector('.signal-time');
            const signalProbability = document.querySelector('.signal-probability');
            const signalEntryPrice = document.querySelector('.signal-entry-price');

            // Направление
            directionIcon.className = `direction-icon ${signal.direction.toLowerCase()}`;
            directionIcon.querySelector('i').className = `fas fa-arrow-${signal.direction === 'CALL' ? 'up' : 'down'}`;
            directionText.textContent = signal.direction;

            // Информация
            signalCurrency.textContent = signal.currency;
            signalTime.textContent = signal.timeframe;
            signalProbability.textContent = `${signal.probability}%`;
            signalEntryPrice.textContent = signal.entryPrice;
        }

        showTradeResult(isWin, signal) {
            const tradeResult = document.getElementById('tradeResult');
            const resultBadge = tradeResult.querySelector('.result-badge');
            
            resultBadge.className = `result-badge ${isWin ? 'win' : 'lose'}`;
            resultBadge.querySelector('.result-text').textContent = isWin ? 'WIN' : 'LOSE';
            resultBadge.querySelector('i').className = `fas fa-${isWin ? 'trophy' : 'times-circle'}`;

            tradeResult.classList.remove('d-none');
            tradeResult.classList.add('zoom-in');

            // Звуковые эффекты
            this.playSound(isWin ? 'win' : 'lose');
            
            // Показать уведомление
            this.showToast(
                isWin ? `🎉 Выигрыш! ${signal.currency} ${signal.direction}` : `😞 Проигрыш. ${signal.currency} ${signal.direction}`,
                isWin ? 'success' : 'danger'
            );

            // Создать конфетти для выигрыша
            if (isWin) {
                this.createConfetti();
            }
        }

        playSound(type) {
            try {
                const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                const frequency = {
                    'start': 440,
                    'win': 523.25,
                    'lose': 261.63,
                    'notification': 880
                }[type] || 440;

                const oscillator = audioContext.createOscillator();
                const gainNode = audioContext.createGain();

                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);

                oscillator.frequency.setValueAtTime(frequency, audioContext.currentTime);
                oscillator.type = type === 'win' ? 'sine' : type === 'lose' ? 'sawtooth' : 'square';

                gainNode.gain.setValueAtTime(0.2, audioContext.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);

                oscillator.start(audioContext.currentTime);
                oscillator.stop(audioContext.currentTime + 0.5);
            } catch (e) {
                console.log('Web Audio API не поддерживается');
            }
        }

        createConfetti() {
            const colors = ['#667eea', '#764ba2', '#00c851', '#ffa726', '#26c6da'];
            
            for (let i = 0; i < 50; i++) {
                const confetti = document.createElement('div');
                confetti.style.cssText = `
                    position: fixed;
                    width: 10px;
                    height: 10px;
                    background: ${colors[Math.floor(Math.random() * colors.length)]};
                    top: -10px;
                    left: ${Math.random() * 100}vw;
                    z-index: 9999;
                    border-radius: 50%;
                `;
                
                document.body.appendChild(confetti);
                
                confetti.animate([
                    { transform: 'translateY(0) rotate(0deg)', opacity: 1 },
                    { transform: `translateY(100vh) rotate(${Math.random() * 360}deg)`, opacity: 0 }
                ], {
                    duration: 3000 + Math.random() * 2000,
                    easing: 'ease-out'
                }).onfinish = () => confetti.remove();
            }
        }

        showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `toast-notification toast-${type}`;
            toast.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 15px 20px;
                background: ${this.getToastColor(type)};
                color: white;
                border-radius: 12px;
                box-shadow: 0 8px 25px rgba(0,0,0,0.3);
                z-index: 9999;
                transform: translateX(100%);
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                font-weight: 600;
                font-size: 0.9rem;
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255,255,255,0.2);
            `;
            toast.textContent = message;

            document.body.appendChild(toast);

            setTimeout(() => {
                toast.style.transform = 'translateX(0)';
            }, 100);

            setTimeout(() => {
                toast.style.transform = 'translateX(100%)';
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }

        getToastColor(type) {
            const colors = {
                'success': 'linear-gradient(135deg, #00c851 0%, #00a040 100%)',
                'danger': 'linear-gradient(135deg, #ff3d00 0%, #cc0000 100%)',
                'warning': 'linear-gradient(135deg, #ffa726 0%, #fb8c00 100%)',
                'info': 'linear-gradient(135deg, #26c6da 0%, #00acc1 100%)'
            };
            return colors[type] || colors.info;
        }

        getTimeframeDuration(timeframe) {
            const durations = {
                '5s': 5000, '15s': 15000, '30s': 30000,
                '1m': 60000, '2m': 120000, '5m': 300000,
                '15m': 900000, '30m': 1800000
            };
            return durations[timeframe] || 60000;
        }

        updateStats(signal, isWin) {
            const key = this.isMartingale ? 'martingale' : 
                      this.selectedCurrency ? `currency_${signal.currency}` : 
                      this.selectedTimeframe ? `timeframe_${signal.timeframe}` : 'general';

            if (!this.stats[key]) {
                this.stats[key] = { wins: 0, total: 0 };
            }

            this.stats[key].total++;
            if (isWin) this.stats[key].wins++;

            this.saveStats();
        }

        updateStatsDisplay() {
            // Обновить статистику в кнопках фильтров
            document.getElementById('martingaleBtn').querySelector('.filter-stats').textContent = 
                `(${this.getWinRate('martingale')}% Win)`;

            document.getElementById('currencyBtn').querySelector('.filter-stats').textContent = 
                `(${this.getWinRate('general')}% Win)`;

            document.getElementById('timeframeBtn').querySelector('.filter-stats').textContent = 
                `(${this.getWinRate('general')}% Win)`;

            // Обновить статистику в модальных окнах
            document.querySelectorAll('.currency-btn').forEach(btn => {
                const currency = btn.dataset.currency;
                const winRate = this.getWinRate(`currency_${currency}`);
                btn.querySelector('.stats-badge').textContent = `${winRate}% Win`;
            });

            document.querySelectorAll('.timeframe-btn').forEach(btn => {
                const timeframe = btn.dataset.timeframe;
                const winRate = this.getWinRate(`timeframe_${timeframe}`);
                btn.querySelector('.stats-badge').textContent = `${winRate}% Win`;
            });
        }

        getWinRate(key) {
            const stat = this.stats[key];
            if (!stat || stat.total === 0) return 0;
            return Math.round((stat.wins / stat.total) * 100);
        }

        loadStats() {
            const saved = localStorage.getItem('signalStats');
            return saved ? JSON.parse(saved) : {};
        }

        saveStats() {
            localStorage.setItem('signalStats', JSON.stringify(this.stats));
        }

        delay(ms) {
            return new Promise(resolve => setTimeout(resolve, ms));
        }
    }

    // Инициализация при загрузке страницы
    document.addEventListener('DOMContentLoaded', function() {
        // Запрос разрешения на уведомления
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }

        // Инициализация генератора сигналов
        const signalGenerator = new SignalGenerator();
        
        // Показать приветственное сообщение
        setTimeout(() => {
            signalGenerator.showToast('🚀 Система торговых сигналов готова к работе!', 'info');
        }, 1000);

        // Добавление эффекта параллакса для карточек
        document.addEventListener('mousemove', (e) => {
            const cards = document.querySelectorAll('.filter-btn, .main-card');
            const mouseX = e.clientX / window.innerWidth;
            const mouseY = e.clientY / window.innerHeight;
            
            cards.forEach((card, index) => {
                const xOffset = (mouseX - 0.5) * 5 * (index + 1);
                const yOffset = (mouseY - 0.5) * 5 * (index + 1);
                
                card.style.transform = `translate(${xOffset}px, ${yOffset}px)`;
            });
        });

        // Добавление hover эффектов к кнопкам
        document.querySelectorAll('.filter-btn, .currency-btn, .timeframe-btn').forEach(btn => {
            btn.addEventListener('mouseenter', () => {
                if (signalGenerator.audioContext) {
                    signalGenerator.playSound('notification');
                }
            });
        });

        // Анимация появления элементов
        const animateElements = () => {
            const elements = document.querySelectorAll('.filter-btn, .user-info-card, .main-card');
            elements.forEach((el, index) => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(50px)';
                
                setTimeout(() => {
                    el.style.transition = 'all 0.8s cubic-bezier(0.4, 0, 0.2, 1)';
                    el.style.opacity = '1';
                    el.style.transform = 'translateY(0)';
                }, index * 150);
            });
        };

        // Запуск анимации появления
        setTimeout(animateElements, 300);

        // Добавление пульсации к кнопке поиска сигнала
        const findBtn = document.getElementById('findSignalBtn');
        setInterval(() => {
            if (!findBtn.disabled) {
                findBtn.style.transform = 'scale(1.02)';
                setTimeout(() => {
                    findBtn.style.transform = 'scale(1)';
                }, 100);
            }
        }, 3000);

        // Горячие клавиши
        document.addEventListener('keydown', (e) => {
            if (e.key === ' ' && !findBtn.disabled) {
                e.preventDefault();
                findBtn.click();
            }
        });

        // Сохранение ссылки на генератор для глобального доступа
        window.signalGenerator = signalGenerator;
    });
</script>
@endsection 