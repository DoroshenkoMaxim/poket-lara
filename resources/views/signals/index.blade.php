@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">


            <!-- Основной интерфейс -->
            <div class="card main-card">
                <div class="card-header text-center">
                    <h3 class="mb-0">
                        <i class="fas fa-chart-line text-primary"></i>
                        PocketOption
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Фильтры -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <button type="button" class="btn btn-outline-primary btn-sm w-100 filter-btn" 
                                    data-bs-toggle="modal" data-bs-target="#currencyModal" id="currencyBtn">
                                <i class="fas fa-coins"></i>
                                <span class="filter-title">Валюты</span>
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button type="button" class="btn btn-outline-info btn-sm w-100 filter-btn" 
                                    data-bs-toggle="modal" data-bs-target="#timeframeModal" id="timeframeBtn">
                                <i class="fas fa-clock"></i>
                                <span class="filter-title">Таймфреймы</span>
                            </button>
                        </div>
                    </div>

                    <!-- Кнопка поиска сигнала -->
                    <div class="text-center mb-4">
                        <button type="button" class="btn btn-success btn-lg px-5 py-3 me-3" id="findSignalBtn">
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

      

                    </div>

                    

                    <!-- Информационный блок -->
                    <div class="alert alert-info mt-4 info-block" role="alert">
                        <div class="row align-items-center">
                            <div class="col-md-12">
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
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-primary w-100 currency-btn" data-currency="GBP/USD">
                            <strong>GBP/USD</strong>
                            <small class="d-block text-muted">Pound/Dollar</small>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-primary w-100 currency-btn" data-currency="USD/JPY">
                            <strong>USD/JPY</strong>
                            <small class="d-block text-muted">Dollar/Yen</small>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-primary w-100 currency-btn" data-currency="AUD/USD">
                            <strong>AUD/USD</strong>
                            <small class="d-block text-muted">Aussie/Dollar</small>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-primary w-100 currency-btn" data-currency="USD/CAD">
                            <strong>USD/CAD</strong>
                            <small class="d-block text-muted">Dollar/Canadian</small>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-primary w-100 currency-btn" data-currency="EUR/GBP">
                            <strong>EUR/GBP</strong>
                            <small class="d-block text-muted">Euro/Pound</small>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-primary w-100 currency-btn" data-currency="USD/CHF">
                            <strong>USD/CHF</strong>
                            <small class="d-block text-muted">Dollar/Franc</small>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-primary w-100 currency-btn" data-currency="NZD/USD">
                            <strong>NZD/USD</strong>
                            <small class="d-block text-muted">Kiwi/Dollar</small>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-primary w-100 currency-btn" data-currency="EUR/JPY">
                            <strong>EUR/JPY</strong>
                            <small class="d-block text-muted">Euro/Yen</small>
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
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-outline-info w-100 timeframe-btn" data-timeframe="15s">
                            <strong>15 секунд</strong>
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-outline-info w-100 timeframe-btn" data-timeframe="30s">
                            <strong>30 секунд</strong>
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-outline-info w-100 timeframe-btn" data-timeframe="1m">
                            <strong>1 минута</strong>
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-outline-info w-100 timeframe-btn" data-timeframe="2m">
                            <strong>2 минуты</strong>
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-outline-info w-100 timeframe-btn" data-timeframe="5m">
                            <strong>5 минут</strong>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Стили -->
<style>
    /* Отключение всех анимаций и эффектов */
    * {
        transition: none !important;
        animation: none !important;
        transform: none !important;
    }

    .btn:hover, .btn:focus, .btn:active,
    .filter-btn:hover, .filter-btn:focus, .filter-btn:active,
    .currency-btn:hover, .currency-btn:focus, .currency-btn:active,
    .timeframe-btn:hover, .timeframe-btn:focus, .timeframe-btn:active,
    .action-btn:hover, .action-btn:focus, .action-btn:active {
        transform: none !important;
        box-shadow: none !important;
        border-color: inherit !important;
        background-color: inherit !important;
        background: inherit !important;
        color: inherit !important;
    }

    .main-card {
        border: none;
        border-radius: 15px;
    }

    .filter-btn {
        height: 100px;
        border-radius: 15px;
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: center;
        gap: 10px;
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

    #findSignalBtn {
        border-radius: 25px;
        background: linear-gradient(135deg, #00c851 0%, #00a040 100%) !important;
        border: none !important;
        font-size: 1.2rem;
        font-weight: 600;
        color: white !important;
    }

    #findSignalBtn:hover,
    #findSignalBtn:focus,
    #findSignalBtn:active,
    #findSignalBtn.focus,
    #findSignalBtn.active {
        background: linear-gradient(135deg, #00c851 0%, #00a040 100%) !important;
        border: none !important;
        color: white !important;
        box-shadow: none !important;
        outline: none !important;
    }

    #findSignalBtn:disabled {
        background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%) !important;
        color: white !important;
        opacity: 0.8;
    }

    #clearFiltersBtn {
        border-radius: 25px;
        font-size: 1.2rem;
        font-weight: 600;
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
        position: relative;
    }

    .signal-card.win {
        background: linear-gradient(135deg, #00c851 0%, #00a040 100%);
    }

    .signal-card.lose {
        background: linear-gradient(135deg, #ff3d00 0%, #cc0000 100%);
    }

    .signal-card .result-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: rgba(0,0,0,0.3);
        color: white;
        padding: 8px 15px;
        border-radius: 20px;
        font-size: 1rem;
        font-weight: 700;
        text-transform: uppercase;
        backdrop-filter: blur(5px);
        border: 1px solid rgba(255,255,255,0.2);
        display: flex;
        align-items: center;
        gap: 5px;
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



    .currency-btn, .timeframe-btn {
        height: 80px;
        position: relative;
        overflow: hidden;
    }

    /* Кнопки действий */
    .action-btn {
        height: 80px;
        border-radius: 15px;
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        background: linear-gradient(135deg, #00c851 0%, #00a040 100%);
        border: none;
    }

    .action-btn.btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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

        /* Адаптивность для мобильных устройств */
    @media (max-width: 768px) {
        .action-btn {
            height: 70px;
            margin-bottom: 15px;
        }
        
        .btn-title {
            font-size: 1rem;
        }

        /* Адаптивность кнопок поиска и сброса на мобильных */
        #findSignalBtn, #clearFiltersBtn {
            font-size: 1rem;
            padding: 12px 20px !important;
            margin-bottom: 10px;
            display: block;
            width: 100%;
        }

        #findSignalBtn {
            margin-right: 0 !important;
        }
        
        /* Компактные кнопки фильтров для мобильных */
        .filter-btn {
            height: 60px;
            padding: 10px;
        }
        
        .filter-btn i {
            font-size: 1.5rem;
        }
        
        /* Отступ между фильтрами на мобильных */
        .row.mb-4 .col-md-6:first-child {
            margin-bottom: 15px;
        }
        
        /* Убираем отступы между колонками на мобильных */
        .row.mb-4 .col-md-6 {
            padding-left: 5px;
            padding-right: 5px;
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
            this.timeframes = ['5s', '15s', '30s', '1m', '2m', '5m'];
            this.selectedCurrency = null;
            this.selectedTimeframe = null;
            this.lastSignal = null;
            this.init();
        }

        init() {
            this.bindEvents();
        }

        bindEvents() {
            // Выбор валюты
            document.querySelectorAll('.currency-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    this.selectCurrency(btn.dataset.currency);
                    this.closeModal('currencyModal');
                });
            });

            // Выбор таймфрейма
            document.querySelectorAll('.timeframe-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    this.selectTimeframe(btn.dataset.timeframe);
                    this.closeModal('timeframeModal');
                });
            });

            // Поиск сигнала
            document.getElementById('findSignalBtn').addEventListener('click', () => {
                this.findSignal();
            });
        }

        selectCurrency(currency) {
            this.selectedCurrency = currency;
            this.updateFilterButtons();
        }

        selectTimeframe(timeframe) {
            this.selectedTimeframe = timeframe;
            this.updateFilterButtons();
        }



        updateFilterButtons() {
            const currencyBtn = document.getElementById('currencyBtn');
            const timeframeBtn = document.getElementById('timeframeBtn');

            // Сброс всех активных состояний
            currencyBtn.classList.remove('active');
            timeframeBtn.classList.remove('active');

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
        }

        async findSignal() {
            const findBtn = document.getElementById('findSignalBtn');
            const loadingAnimation = document.getElementById('loadingAnimation');
            const signalResult = document.getElementById('signalResult');

            // Очистить предыдущий сигнал
            this.lastSignal = null;

            // Показать загрузку
            findBtn.disabled = true;
            findBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span class="btn-text">Поиск сигнала...</span>';
            signalResult.classList.add('d-none');
            loadingAnimation.classList.remove('d-none');
            
            // Показать сообщения поиска
            this.showSearchMessages();

            // Симуляция поиска
            await this.delay(3000);

            // Сгенерировать сигнал
            const signal = this.generateSignal();
            this.lastSignal = signal;
            this.displaySignal(signal);

            // Скрыть загрузку и показать результат
            loadingAnimation.classList.add('d-none');
            signalResult.classList.remove('d-none');
            
            // Скрыть кнопку поиска сигнала
            findBtn.style.display = 'none';

            // Ждем завершения торговли
            const duration = this.getTimeframeDuration(signal.timeframe);
            await this.waitForTradeCompletion(duration, signal);

            // Определить результат и показать
            const isWin = Math.random() > 0.25; // 75% шанс выигрыша
            this.lastSignal.result = isWin;
            this.showTradeResult(isWin, signal);

            // Показать кнопку снова через 3 секунды
            setTimeout(() => {
                findBtn.style.display = 'inline-block';
                findBtn.disabled = false;
                findBtn.innerHTML = '<i class="fas fa-search"></i> <span class="btn-text">Найти сигнал</span>';
            }, 3000);
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
            // По умолчанию используем случайные значения для всего рынка
            const currency = this.selectedCurrency || this.currencies[Math.floor(Math.random() * this.currencies.length)];
            const timeframe = this.selectedTimeframe || this.timeframes[Math.floor(Math.random() * this.timeframes.length)];
            const direction = Math.random() > 0.5 ? 'CALL' : 'PUT';

            return {
                currency,
                timeframe,
                direction,
                probability: Math.floor(Math.random() * 30) + 70, // 70-99%
                entryPrice: this.generatePrice(currency),
                timestamp: new Date()
            };
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
            const signalCard = document.querySelector('.signal-card');
            const directionIcon = document.querySelector('.direction-icon');
            const directionText = document.querySelector('.direction-text');
            const signalCurrency = document.querySelector('.signal-currency');
            const signalTime = document.querySelector('.signal-time');
            const signalProbability = document.querySelector('.signal-probability');
            const signalEntryPrice = document.querySelector('.signal-entry-price');

            // Сбросить цвет карточки на исходный
            signalCard.className = 'signal-card';
            
            // Удалить предыдущий значок результата
            const existingBadge = signalCard.querySelector('.result-badge');
            if (existingBadge) {
                existingBadge.remove();
            }

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
            const signalCard = document.querySelector('.signal-card');
            
            // Изменить цвет карточки
            signalCard.className = `signal-card ${isWin ? 'win' : 'lose'}`;
            
            // Добавить значок результата в правый верхний угол
            const existingBadge = signalCard.querySelector('.result-badge');
            if (existingBadge) {
                existingBadge.remove();
            }
            
            const resultBadge = document.createElement('div');
            resultBadge.className = 'result-badge';
            resultBadge.innerHTML = `
                <i class="fas fa-${isWin ? 'trophy' : 'times-circle'}"></i>
                <span>${isWin ? 'WIN' : 'LOSE'}</span>
            `;
            
            signalCard.appendChild(resultBadge);
        }

        getTimeframeDuration(timeframe) {
            const durations = {
                '5s': 5000, '15s': 15000, '30s': 30000,
                '1m': 60000, '2m': 120000, '5m': 300000,
            };
            return durations[timeframe] || 60000;
        }

        delay(ms) {
            return new Promise(resolve => setTimeout(resolve, ms));
        }

        closeModal(modalId) {
            const modalElement = document.getElementById(modalId);
            
            // Закрыть модальное окно
            if (modalElement) {
                modalElement.classList.remove('show');
                modalElement.style.display = 'none';
                modalElement.setAttribute('aria-hidden', 'true');
                modalElement.setAttribute('aria-modal', 'false');
            }
            
            // Удалить все backdrop элементы
            const backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(backdrop => backdrop.remove());
            
            // Очистить состояние body
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
            
            // Очистить data-атрибуты
            document.body.removeAttribute('data-bs-overflow');
            document.body.removeAttribute('data-bs-padding-right');
        }
    }

    // Инициализация при загрузке страницы
    document.addEventListener('DOMContentLoaded', function() {
        // Инициализация генератора сигналов
        const signalGenerator = new SignalGenerator();

        // Сохранение ссылки на генератор для глобального доступа
        window.signalGenerator = signalGenerator;
    });
</script>
@endsection 