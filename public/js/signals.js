/**
 * Торговые сигналы - Упрощенный JavaScript файл
 */

class SimpleSignalGenerator {
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
        this.timeframes = ['30s', '1m', '2m', '3m', '4m', '5m'];
        this.selectedCurrency = null;
        this.selectedTimeframe = null;
        this.isMartingale = false;
        this.lastSignal = null;
        this.isGenerating = false;
        this.init();
    }

    init() {
        this.bindEvents();
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
            if (this.currencyClickHandler) {
                signalCurrency.removeEventListener('click', this.currencyClickHandler);
            }
            
            this.currencyClickHandler = (e) => {
                this.copyCurrencyToClipboard(e.target.textContent);
            };
            
            signalCurrency.addEventListener('click', this.currencyClickHandler);
        }
    }

    async copyCurrencyToClipboard(currencyText) {
        try {
            if (navigator.clipboard && window.isSecureContext) {
                await navigator.clipboard.writeText(currencyText);
            } else {
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
        const notification = document.createElement('div');
        notification.innerHTML = success 
            ? `${currencyText} скопировано!`
            : `Ошибка копирования`;
        
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${success ? '#28a745' : '#dc3545'};
            color: white;
            padding: 10px 15px;
            border-radius: 4px;
            z-index: 10000;
        `;

        document.body.appendChild(notification);

        setTimeout(() => {
            document.body.removeChild(notification);
        }, 3000);
    }

    toggleMartingale() {
        this.isMartingale = !this.isMartingale;
        const btn = document.getElementById('martingaleBtn');
        
        if (this.isMartingale) {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
        
        this.updateFilterButtons();
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
        const martingaleBtn = document.getElementById('martingaleBtn');

        if (this.selectedCurrency) {
            currencyBtn.classList.add('active');
            currencyBtn.querySelector('.filter-title').textContent = this.selectedCurrency;
            currencyBtn.querySelector('.filter-subtitle').textContent = 'Выбрана валюта';
        } else {
            currencyBtn.classList.remove('active');
            currencyBtn.querySelector('.filter-title').textContent = 'ВАЛЮТА';
            currencyBtn.querySelector('.filter-subtitle').textContent = 'Выберите валютную пару';
        }

        if (this.selectedTimeframe) {
            timeframeBtn.classList.add('active');
            timeframeBtn.querySelector('.filter-title').textContent = this.selectedTimeframe;
            timeframeBtn.querySelector('.filter-subtitle').textContent = 'Выбран таймфрейм';
        } else {
            timeframeBtn.classList.remove('active');
            timeframeBtn.querySelector('.filter-title').textContent = 'ВРЕМЯ';
            timeframeBtn.querySelector('.filter-subtitle').textContent = 'Выберите таймфрейм';
        }
    }

    async findSignal() {
        if (this.isGenerating) return;
        
        this.isGenerating = true;
        const btn = document.getElementById('findSignalBtn');
        const originalText = btn.textContent;
        
        btn.textContent = 'Поиск сигнала...';
        btn.disabled = true;
        
        this.showLoadingMessages();
        
        await this.delay(3000);
        
        const signal = this.generateSignal();
        this.displaySignal(signal);
        
        const duration = this.getTimeframeDuration(signal.timeframe);
        await this.waitForTradeCompletion(duration, signal);
        
        btn.textContent = originalText;
        btn.disabled = false;
        this.isGenerating = false;
    }

    showLoadingMessages() {
        const resultContainer = document.getElementById('signalResult');
        resultContainer.innerHTML = `
            <div class="loading-animation">
                <div class="spinner-container">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Загрузка...</span>
                    </div>
                    <p class="mt-3 mb-0">Поиск лучшего сигнала...</p>
                </div>
            </div>
        `;
    }

    generateSignal() {
        const currency = this.selectedCurrency || this.currencies[Math.floor(Math.random() * this.currencies.length)];
        const timeframe = this.selectedTimeframe || this.timeframes[Math.floor(Math.random() * this.timeframes.length)];
        const direction = Math.random() > 0.5 ? 'вверх' : 'вниз';
        
        let actualTimeframe = timeframe;
        if (this.isMartingale && this.lastSignal && this.lastSignal.isWin === false) {
            actualTimeframe = this.getNextSmallerTimeframe(timeframe);
        }
        
        const signal = {
            currency: currency,
            direction: direction,
            timeframe: actualTimeframe,
            price: this.generatePrice(currency),
            accuracy: 78 + Math.floor(Math.random() * 15),
            timestamp: new Date(),
            id: Date.now()
        };
        
        this.lastSignal = signal;
        return signal;
    }

    displaySignal(signal) {
        const resultContainer = document.getElementById('signalResult');
        
        resultContainer.innerHTML = `
            <div class="signal-result">
                <div class="signal-card">
                    <div class="signal-direction">
                        <div class="direction-icon ${signal.direction}">
                            <i class="fas fa-arrow-${signal.direction === 'вверх' ? 'up' : 'down'}"></i>
                        </div>
                        <h3 class="direction-text">${signal.direction.toUpperCase()}</h3>
                    </div>
                    
                    <div class="signal-info">
                        <div class="signal-currency">${signal.currency}</div>
                        
                        <div class="signal-details">
                            <div class="detail-item">
                                <span class="detail-label">Таймфрейм:</span>
                                <span class="detail-value">${signal.timeframe}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Точность:</span>
                                <span class="detail-value">${signal.accuracy}%</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Цена входа:</span>
                                <span class="detail-value">${signal.price}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Время:</span>
                                <span class="detail-value">${signal.timestamp.toLocaleTimeString()}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        this.setupCurrencyCopyListener();
    }

    async waitForTradeCompletion(duration, signal) {
        await this.delay(duration);
        
        const isWin = Math.random() < (signal.accuracy / 100);
        this.showTradeResult(isWin, signal);
        this.updateStats(signal, isWin);
    }

    showTradeResult(isWin, signal) {
        const resultContainer = document.getElementById('signalResult');
        const existingResult = resultContainer.querySelector('.trade-result');
        
        if (existingResult) {
            existingResult.remove();
        }
        
        const resultDiv = document.createElement('div');
        resultDiv.className = 'trade-result';
        resultDiv.innerHTML = `
            <div class="result-badge ${isWin ? 'win' : 'lose'}">
                ${isWin ? 'ВЫИГРЫШ' : 'ПРОИГРЫШ'}
            </div>
        `;
        
        resultContainer.querySelector('.signal-result').appendChild(resultDiv);
        
        signal.isWin = isWin;
    }

    getNextSmallerTimeframe(currentTimeframe) {
        const timeframes = ['30s', '1m', '2m', '3m', '4m', '5m'];
        const currentIndex = timeframes.indexOf(currentTimeframe);
        return currentIndex > 0 ? timeframes[currentIndex - 1] : currentTimeframe;
    }

    generatePrice(currency) {
        const priceRanges = {
            'AED/CNY': [1.85, 1.95],
            'AUD/CAD': [0.90, 0.95],
            'AUD/CHF': [0.55, 0.65],
            'AUD/JPY': [95, 105],
            'AUD/NZD': [1.05, 1.15],
            'AUD/USD': [0.60, 0.70],
            'BHD/CNY': [18, 19],
            'CAD/CHF': [0.65, 0.75],
            'CAD/JPY': [105, 115],
            'CHF/JPY': [160, 170],
            'CHF/NOK': [11, 12],
            'EUR/AUD': [1.55, 1.65],
            'EUR/CAD': [1.45, 1.55],
            'EUR/CHF': [0.95, 1.05],
            'EUR/GBP': [0.85, 0.90],
            'EUR/HUF': [390, 410],
            'EUR/JPY': [155, 165],
            'EUR/NZD': [1.75, 1.85],
            'EUR/TRY': [35, 37],
            'EUR/USD': [1.05, 1.15],
            'GBP/AUD': [1.85, 1.95],
            'GBP/CAD': [1.65, 1.75],
            'GBP/CHF': [1.10, 1.20],
            'GBP/JPY': [185, 195],
            'GBP/USD': [1.25, 1.35],
        };
        
        const range = priceRanges[currency] || [1.0, 2.0];
        const price = range[0] + Math.random() * (range[1] - range[0]);
        return price.toFixed(currency.includes('JPY') ? 2 : 4);
    }

    getTimeframeDuration(timeframe) {
        const durations = {
            '30s': 30000,
            '1m': 60000,
            '2m': 120000,
            '3m': 180000,
            '4m': 240000,
            '5m': 300000
        };
        return durations[timeframe] || 60000;
    }

    updateStats(signal, isWin) {
        let stats = this.loadStats();
        
        const key = `${signal.currency}_${signal.timeframe}`;
        if (!stats[key]) {
            stats[key] = { wins: 0, losses: 0 };
        }
        
        if (isWin) {
            stats[key].wins++;
        } else {
            stats[key].losses++;
        }
        
        this.saveStats(stats);
        this.updateStatsDisplay();
    }

    updateStatsDisplay() {
        const stats = this.loadStats();
        
        document.querySelectorAll('.stats-badge').forEach(badge => {
            const btn = badge.closest('.currency-btn, .timeframe-btn');
            if (btn && btn.dataset.currency) {
                const currency = btn.dataset.currency;
                const winRate = this.getWinRate(`${currency}_${this.selectedTimeframe || '1m'}`);
                badge.textContent = `${winRate}%`;
            }
        });
    }

    getWinRate(key) {
        const stats = this.loadStats();
        if (!stats[key] || (stats[key].wins + stats[key].losses) === 0) {
            return 0;
        }
        return Math.round((stats[key].wins / (stats[key].wins + stats[key].losses)) * 100);
    }

    loadStats() {
        return JSON.parse(localStorage.getItem('signalStats') || '{}');
    }

    saveStats(stats) {
        localStorage.setItem('signalStats', JSON.stringify(stats));
    }

    delay(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }
}

// Инициализация после загрузки DOM
document.addEventListener('DOMContentLoaded', () => {
    new SimpleSignalGenerator();
}); 