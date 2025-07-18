/**
 * Торговые сигналы - Главный JavaScript файл
 * Включает звуковые эффекты, анимации и улучшенную логику
 */

class AdvancedSignalGenerator {
    constructor() {
        this.currencies = [
            'AED/CNY', 'AUD/CAD', 'AUD/CHF', 'AUD/JPY', 'AUD/NZD', 'AUD/USD',
            'BHD/CNY', 'CAD/CHF', 'CAD/JPY', 'CHF/JPY', 'CHF/NOK',
            'EUR/AUD', 'EUR/CAD', 'EUR/CHF', 'EUR/GBP', 'EUR/HUF', 'EUR/JPY', 
            'EUR/NZD', 'EUR/TRY', 'EUR/USD', 'GBP/AUD', 'GBP/CAD', 'GBP/CHF', 
            'GBP/JPY', 'GBP/USD', 'JOD/CNY', 'KES/USD', 'LBP/USD', 'MAD/USD', 
            'NGN/USD', 'NZD/JPY', 'NZD/USD', 'OMR/CNY', 'QAR/CNY', 'SAR/CNY', 
            'TND/USD', 'UAH/USD', 'USD/ARS', 'USD/BDT', 'USD/BRL', 'USD/CAD', 
            'USD/CHF', 'USD/CLP', 'USD/CNH', 'USD/COP', 'USD/DZD', 'USD/EGP', 
            'USD/IDR', 'USD/INR', 'USD/JPY', 'USD/MXN', 'USD/MYR', 'USD/PHP', 
            'USD/PKR', 'USD/SGD', 'USD/THB', 'USD/VND', 'YER/USD', 'ZAR/USD'
        ];
        this.timeframes = ['30s', '1m', '2m', '3m', '4m', '5m',];
        this.selectedCurrency = null;
        this.selectedTimeframe = null;
        this.isMartingale = false;
        this.lastSignal = null;

        this.audioContext = null;
        this.isGenerating = false;
        this.init();
    }

    init() {
        this.initAudio();
        this.bindEvents();
    }

    initAudio() {
        try {
            this.audioContext = new (window.AudioContext || window.webkitAudioContext)();
        } catch (e) {
            console.log('Web Audio API не поддерживается');
        }
    }

    playSound(type) {
        if (!this.audioContext) return;

        const frequency = {
            'start': 440,    // A4
            'win': 523.25,   // C5
            'lose': 261.63,  // C4
            'notification': 880 // A5
        }[type] || 440;

        const oscillator = this.audioContext.createOscillator();
        const gainNode = this.audioContext.createGain();

        oscillator.connect(gainNode);
        gainNode.connect(this.audioContext.destination);

        oscillator.frequency.setValueAtTime(frequency, this.audioContext.currentTime);
        oscillator.type = type === 'win' ? 'sine' : type === 'lose' ? 'sawtooth' : 'square';

        gainNode.gain.setValueAtTime(0.3, this.audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, this.audioContext.currentTime + 0.5);

        oscillator.start(this.audioContext.currentTime);
        oscillator.stop(this.audioContext.currentTime + 0.5);
    }

    showWelcomeAnimation() {
        // Анимация отключена для экономии ресурсов
    }

    createParticleEffect() {
        // Частицы отключены для экономии ресурсов
    }

    animateParticle(particle) {
        // Анимация частиц отключена для экономии ресурсов
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
            if (!this.isGenerating) {
                this.findSignal();
            }
        });

        // Копирование названия валюты при клике
        this.setupCurrencyCopyListener();
    }

    setupCurrencyCopyListener() {
        const signalCurrency = document.querySelector('.signal-currency');
        if (signalCurrency) {
            // Удаляем старый обработчик, если он есть
            if (this.currencyClickHandler) {
                signalCurrency.removeEventListener('click', this.currencyClickHandler);
            }
            
            // Создаем новый обработчик и сохраняем ссылку на него
            this.currencyClickHandler = (e) => {
                this.copyCurrencyToClipboard(e.target.textContent);
            };
            
            signalCurrency.addEventListener('click', this.currencyClickHandler);
        }
    }

    async copyCurrencyToClipboard(currencyText) {
        try {
            // Используем современный API clipboard если доступен
            if (navigator.clipboard && window.isSecureContext) {
                await navigator.clipboard.writeText(currencyText);
            } else {
                // Fallback для старых браузеров
                const textArea = document.createElement('textarea');
                textArea.value = currencyText;
                textArea.style.position = 'absolute';
                textArea.style.left = '-999999px';
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
            }
            
            this.showCopyNotification(currencyText);
        } catch (err) {
            console.error('Ошибка копирования:', err);
            this.showCopyNotification(currencyText, false);
        }
    }

    showCopyNotification(currencyText, success = true) {
        // Создаем уведомление
        const notification = document.createElement('div');
        notification.className = 'copy-notification';
        notification.innerHTML = success 
            ? `<i class="fas fa-check"></i> ${currencyText} скопировано!`
            : `<i class="fas fa-exclamation"></i> Ошибка копирования`;
        
        // Стили уведомления
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${success ? '#28a745' : '#dc3545'};
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 600;
            z-index: 10000;
            animation: slideInRight 0.3s ease-out;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        `;

        // Добавляем анимацию
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideInRight {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOutRight {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(100%); opacity: 0; }
            }
        `;
        
        if (!document.querySelector('style[data-copy-animation]')) {
            style.setAttribute('data-copy-animation', 'true');
            document.head.appendChild(style);
        }

        document.body.appendChild(notification);

        // Удаляем уведомление через 3 секунды
        setTimeout(() => {
            notification.style.animation = 'slideOutRight 0.3s ease-out';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }

    addHoverEffects() {
        // Hover эффекты отключены для экономии ресурсов
    }

    toggleMartingale() {
        this.isMartingale = !this.isMartingale;
        const btn = document.getElementById('martingaleBtn');
        
        if (this.isMartingale) {
            btn.classList.add('active');
            this.selectedCurrency = null;
            this.selectedTimeframe = null;
            this.updateFilterButtons();
        } else {
            btn.classList.remove('active');
        }
    }

    selectCurrency(currency) {
        this.selectedCurrency = currency;
        this.isMartingale = false;
        this.updateFilterButtons();
    }

    selectTimeframe(timeframe) {
        this.selectedTimeframe = timeframe;
        this.isMartingale = false;
        this.updateFilterButtons();
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
        this.isGenerating = true;
        const findBtn = document.getElementById('findSignalBtn');
        const loadingAnimation = document.getElementById('loadingAnimation');
        const signalResult = document.getElementById('signalResult');
        const tradeResult = document.getElementById('tradeResult');

        try {
            // Показать загрузку с анимацией
            findBtn.disabled = true;
            findBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span class="btn-text">Поиск...</span>';
            signalResult.classList.add('d-none');
            tradeResult.classList.add('d-none');
            loadingAnimation.classList.remove('d-none');
            
            this.playSound('start');
            this.showLoadingMessages();

            // Симуляция сложного поиска
            await this.delay(3000);

            // Сгенерировать сигнал
            const signal = await this.generateSignalFromAPI();
            this.displaySignal(signal);

            // Скрыть загрузку и показать результат
            loadingAnimation.classList.add('d-none');
            signalResult.classList.remove('d-none');

            // Ждем завершения торговли
            const duration = this.getTimeframeDuration(signal.timeframe);
            await this.waitForTradeCompletion(duration, signal);

            // Определить результат и показать
            const isWin = Math.random() > 0.25; // 75% шанс выигрыша
            this.showTradeResult(isWin, signal);



            // Сохранить сигнал для мартингейла
            this.lastSignal = { ...signal, result: isWin };

        } catch (error) {
            console.error('Ошибка генерации сигнала:', error);
        } finally {
            findBtn.disabled = false;
            findBtn.innerHTML = '<i class="fas fa-search"></i> <span class="btn-text">Найти сигнал</span>';
            this.isGenerating = false;
        }
    }

    showLoadingMessages() {
        const messages = [
            'Анализ рынка...',
            'Поиск паттернов...',
            'Вычисление вероятностей...',
            'Готов к торговле!'
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

    async generateSignalFromAPI() {
        // В реальном приложении здесь будет вызов API
        // Пока симулируем генерацию
        return this.generateSignal();
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
            direction = Math.random() > 0.5 ? 'ВВЕРХ' : 'ВНИЗ';
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

    displaySignal(signal) {
        const directionIcon = document.querySelector('.direction-icon');
        const directionText = document.querySelector('.direction-text');
        const signalCurrency = document.querySelector('.signal-currency');
        const signalTime = document.querySelector('.signal-time');
        const signalProbability = document.querySelector('.signal-probability');
        const signalEntryPrice = document.querySelector('.signal-entry-price');

        // Направление с анимацией
        directionIcon.className = `direction-icon ${signal.direction.toLowerCase()}`;
        directionIcon.querySelector('i').className = `fas fa-arrow-${signal.direction === 'ВВЕРХ' ? 'up' : 'down'}`;
        directionText.textContent = signal.direction;

        // Информация с анимацией появления
        signalCurrency.textContent = signal.currency;
        signalTime.textContent = signal.timeframe;
        signalProbability.textContent = `${signal.probability}%`;
        signalEntryPrice.textContent = signal.entryPrice;

        // Добавляем пульсацию к иконке направления
        directionIcon.style.animation = 'pulse 1s ease-in-out 3';

        // Перепривязываем обработчик клика для копирования валюты
        this.setupCurrencyCopyListener();
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
                    if (progressBar) progressBar.remove();
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
        `;

        signalCard.appendChild(countdown);
        return countdown;
    }

    showTradeResult(isWin, signal) {
        const tradeResult = document.getElementById('tradeResult');
        const resultBadge = tradeResult.querySelector('.result-badge');
        
        resultBadge.className = `result-badge ${isWin ? 'win' : 'lose'}`;
        resultBadge.querySelector('.result-text').textContent = isWin ? 'WIN' : 'LOSE';
        resultBadge.querySelector('i').className = `fas fa-${isWin ? 'trophy' : 'times-circle'}`;

        tradeResult.classList.remove('d-none');
        tradeResult.classList.add('zoom-in');

        // Звуковое уведомление
        this.playSound(isWin ? 'win' : 'lose');
        

    }

    createConfetti() {
        // Конфетти отключено для экономии ресурсов
    }

    showToast(message, type = 'info') {
        // Уведомления отключены для экономии ресурсов
    }

    getToastColor(type) {
        // Цвета отключены для экономии ресурсов
        return '#26c6da';
    }

    // Остальные методы остаются такими же...
    getNextSmallerTimeframe(currentTimeframe) {
        const index = this.timeframes.indexOf(currentTimeframe);
        return index > 0 ? this.timeframes[index - 1] : this.timeframes[0];
    }

    generatePrice(currency) {
        const prices = {
            // Major Pairs
            'EUR/USD': 1.0800 + Math.random() * 0.0200,
            'GBP/USD': 1.2500 + Math.random() * 0.0300,
            'USD/JPY': 145.00 + Math.random() * 5.00,
            'AUD/USD': 0.6600 + Math.random() * 0.0200,
            'USD/CAD': 1.3500 + Math.random() * 0.0200,
            'USD/CHF': 0.9100 + Math.random() * 0.0200,
            'NZD/USD': 0.6100 + Math.random() * 0.0200,
            
            // Cross Pairs
            'EUR/GBP': 0.8600 + Math.random() * 0.0200,
            'EUR/JPY': 156.00 + Math.random() * 4.00,
            'EUR/CHF': 0.9800 + Math.random() * 0.0200,
            'EUR/AUD': 1.6200 + Math.random() * 0.0300,
            'EUR/CAD': 1.4600 + Math.random() * 0.0300,
            'EUR/NZD': 1.7500 + Math.random() * 0.0400,
            'EUR/TRY': 29.50 + Math.random() * 10.00,
            'EUR/HUF': 385.00 + Math.random() * 20.00,
            'GBP/JPY': 181.00 + Math.random() * 5.00,
            'GBP/CHF': 1.1400 + Math.random() * 0.0200,
            'GBP/AUD': 1.8800 + Math.random() * 0.0400,
            'GBP/CAD': 1.7000 + Math.random() * 0.0300,
            'AUD/CAD': 0.9000 + Math.random() * 0.0200,
            'AUD/CHF': 0.6000 + Math.random() * 0.0150,
            'AUD/JPY': 95.50 + Math.random() * 3.00,
            'AUD/NZD': 1.0800 + Math.random() * 0.0200,
            'CAD/CHF': 0.6750 + Math.random() * 0.0150,
            'CAD/JPY': 107.00 + Math.random() * 3.00,
            'CHF/JPY': 159.00 + Math.random() * 4.00,
            'CHF/NOK': 11.20 + Math.random() * 3.00,
            'NZD/JPY': 88.50 + Math.random() * 3.00,
            
            // Exotic and OTC Pairs
            'AED/CNY': 1.96 + Math.random() * 0.05,
            'BHD/CNY': 19.15 + Math.random() * 2.00,
            'JOD/CNY': 10.15 + Math.random() * 1.00,
            'OMR/CNY': 18.75 + Math.random() * 2.00,
            'QAR/CNY': 1.98 + Math.random() * 0.05,
            'SAR/CNY': 1.92 + Math.random() * 0.05,
            'KES/USD': 0.0067 + Math.random() * 0.001,
            'LBP/USD': 0.0000666 + Math.random() * 0.00005,
            'MAD/USD': 0.1000 + Math.random() * 0.002,
            'NGN/USD': 0.0012 + Math.random() * 0.0005,
            'TND/USD': 0.3200 + Math.random() * 0.005,
            'UAH/USD': 0.0270 + Math.random() * 0.002,
            'YER/USD': 0.0040 + Math.random() * 0.0005,
            'ZAR/USD': 0.0530 + Math.random() * 0.003,
            'USD/ARS': 850.00 + Math.random() * 50.00,
            'USD/BDT': 110.50 + Math.random() * 3.00,
            'USD/BRL': 5.15 + Math.random() * 2.00,
            'USD/CLP': 970.00 + Math.random() * 30.00,
            'USD/CNH': 7.20 + Math.random() * 1.00,
            'USD/COP': 4300.00 + Math.random() * 200.00,
            'USD/DZD': 135.00 + Math.random() * 5.00,
            'USD/EGP': 31.00 + Math.random() * 2.00,
            'USD/IDR': 15800.00 + Math.random() * 500.00,
            'USD/INR': 83.20 + Math.random() * 3.00,
            'USD/MXN': 18.50 + Math.random() * 2.00,
            'USD/MYR': 4.70 + Math.random() * 1.00,
            'USD/PHP': 56.50 + Math.random() * 2.00,
            'USD/PKR': 285.00 + Math.random() * 10.00,
            'USD/SGD': 1.36 + Math.random() * 0.05,
            'USD/THB': 36.80 + Math.random() * 2.00,
            'USD/VND': 24300.00 + Math.random() * 1000.00,
        };
        return prices[currency]?.toFixed(4) || '1.0000';
    }

    getTimeframeDuration(timeframe) {
        const durations = {
            '30s': 30000, '1m': 60000, '2m': 120000, '3m': 180000, '4m': 240000, '5m': 300000,
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
    window.signalGenerator = new AdvancedSignalGenerator();

    // Параллакс эффект отключен для экономии ресурсов
}); 