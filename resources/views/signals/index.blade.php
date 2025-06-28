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
                                <div class="direction-text">ВВЕРХ</div>
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
                <!-- Поиск -->
                <div class="mb-3">
                    <input type="text" class="form-control" id="currencySearch" placeholder="🔍 Поиск валютной пары...">
                </div>
                
                <!-- Основные валютные пары -->
                <h6 class="mb-3 text-primary">📈 Основные пары</h6>
                <div class="row g-2 mb-4">
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
                </div>

                <!-- Кросс-пары -->
                <h6 class="mb-3 text-success">🔄 Кросс-пары</h6>
                <div class="row g-2 mb-4">
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-success w-100 currency-btn" data-currency="EUR/GBP">
                            <strong>EUR/GBP</strong>
                            <small class="d-block text-muted">Euro/Pound</small>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-success w-100 currency-btn" data-currency="EUR/JPY">
                            <strong>EUR/JPY</strong>
                            <small class="d-block text-muted">Euro/Yen</small>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-success w-100 currency-btn" data-currency="EUR/CHF">
                            <strong>EUR/CHF</strong>
                            <small class="d-block text-muted">Euro/Franc</small>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-success w-100 currency-btn" data-currency="EUR/AUD">
                            <strong>EUR/AUD</strong>
                            <small class="d-block text-muted">Euro/Aussie</small>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-success w-100 currency-btn" data-currency="EUR/CAD">
                            <strong>EUR/CAD</strong>
                            <small class="d-block text-muted">Euro/Canadian</small>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-success w-100 currency-btn" data-currency="EUR/NZD">
                            <strong>EUR/NZD</strong>
                            <small class="d-block text-muted">Euro/Kiwi</small>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-success w-100 currency-btn" data-currency="GBP/JPY">
                            <strong>GBP/JPY</strong>
                            <small class="d-block text-muted">Pound/Yen</small>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-success w-100 currency-btn" data-currency="GBP/CHF">
                            <strong>GBP/CHF</strong>
                            <small class="d-block text-muted">Pound/Franc</small>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-success w-100 currency-btn" data-currency="GBP/AUD">
                            <strong>GBP/AUD</strong>
                            <small class="d-block text-muted">Pound/Aussie</small>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-success w-100 currency-btn" data-currency="GBP/CAD">
                            <strong>GBP/CAD</strong>
                            <small class="d-block text-muted">Pound/Canadian</small>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-success w-100 currency-btn" data-currency="AUD/CAD">
                            <strong>AUD/CAD</strong>
                            <small class="d-block text-muted">Aussie/Canadian</small>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-success w-100 currency-btn" data-currency="AUD/CHF">
                            <strong>AUD/CHF</strong>
                            <small class="d-block text-muted">Aussie/Franc</small>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-success w-100 currency-btn" data-currency="AUD/JPY">
                            <strong>AUD/JPY</strong>
                            <small class="d-block text-muted">Aussie/Yen</small>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-success w-100 currency-btn" data-currency="AUD/NZD">
                            <strong>AUD/NZD</strong>
                            <small class="d-block text-muted">Aussie/Kiwi</small>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-success w-100 currency-btn" data-currency="CAD/CHF">
                            <strong>CAD/CHF</strong>
                            <small class="d-block text-muted">Canadian/Franc</small>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-success w-100 currency-btn" data-currency="CAD/JPY">
                            <strong>CAD/JPY</strong>
                            <small class="d-block text-muted">Canadian/Yen</small>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-success w-100 currency-btn" data-currency="CHF/JPY">
                            <strong>CHF/JPY</strong>
                            <small class="d-block text-muted">Franc/Yen</small>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-success w-100 currency-btn" data-currency="CHF/NOK">
                            <strong>CHF/NOK</strong>
                            <small class="d-block text-muted">Franc/Krone</small>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-success w-100 currency-btn" data-currency="NZD/JPY">
                            <strong>NZD/JPY</strong>
                            <small class="d-block text-muted">Kiwi/Yen</small>
                        </button>
                    </div>
                </div>

                <!-- Экзотические пары -->
                <h6 class="mb-3 text-warning">🌍 Экзотические пары</h6>
                <div class="row g-2 mb-4">
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-warning w-100 currency-btn" data-currency="EUR/TRY">
                            <strong>EUR/TRY</strong>
                            <small class="d-block text-muted">Euro/Lira</small>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-warning w-100 currency-btn" data-currency="EUR/HUF">
                            <strong>EUR/HUF</strong>
                            <small class="d-block text-muted">Euro/Forint</small>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-warning w-100 currency-btn" data-currency="USD/CNH">
                            <strong>USD/CNH</strong>
                            <small class="d-block text-muted">Dollar/Yuan</small>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-warning w-100 currency-btn" data-currency="USD/BRL">
                            <strong>USD/BRL</strong>
                            <small class="d-block text-muted">Dollar/Real</small>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-warning w-100 currency-btn" data-currency="USD/MXN">
                            <strong>USD/MXN</strong>
                            <small class="d-block text-muted">Dollar/Peso</small>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-warning w-100 currency-btn" data-currency="USD/INR">
                            <strong>USD/INR</strong>
                            <small class="d-block text-muted">Dollar/Rupee</small>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-warning w-100 currency-btn" data-currency="USD/SGD">
                            <strong>USD/SGD</strong>
                            <small class="d-block text-muted">Dollar/Singapore</small>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-warning w-100 currency-btn" data-currency="USD/THB">
                            <strong>USD/THB</strong>
                            <small class="d-block text-muted">Dollar/Baht</small>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-warning w-100 currency-btn" data-currency="ZAR/USD">
                            <strong>ZAR/USD</strong>
                            <small class="d-block text-muted">Rand/Dollar</small>
                        </button>
                    </div>
                </div>

                <!-- OTC Пары -->
                <h6 class="mb-3 text-info">⏰ OTC Пары</h6>
                <div class="row g-2">
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-info w-100 currency-btn" data-currency="AED/CNY">
                            <strong>AED/CNY</strong>
                            <small class="d-block text-muted">Dirham/Yuan</small>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-info w-100 currency-btn" data-currency="BHD/CNY">
                            <strong>BHD/CNY</strong>
                            <small class="d-block text-muted">Dinar/Yuan</small>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-info w-100 currency-btn" data-currency="JOD/CNY">
                            <strong>JOD/CNY</strong>
                            <small class="d-block text-muted">Dinar/Yuan</small>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-info w-100 currency-btn" data-currency="OMR/CNY">
                            <strong>OMR/CNY</strong>
                            <small class="d-block text-muted">Rial/Yuan</small>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-info w-100 currency-btn" data-currency="QAR/CNY">
                            <strong>QAR/CNY</strong>
                            <small class="d-block text-muted">Riyal/Yuan</small>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-info w-100 currency-btn" data-currency="SAR/CNY">
                            <strong>SAR/CNY</strong>
                            <small class="d-block text-muted">Riyal/Yuan</small>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-info w-100 currency-btn" data-currency="KES/USD">
                            <strong>KES/USD</strong>
                            <small class="d-block text-muted">Shilling/Dollar</small>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-info w-100 currency-btn" data-currency="NGN/USD">
                            <strong>NGN/USD</strong>
                            <small class="d-block text-muted">Naira/Dollar</small>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-info w-100 currency-btn" data-currency="USD/ARS">
                            <strong>USD/ARS</strong>
                            <small class="d-block text-muted">Dollar/Peso</small>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-info w-100 currency-btn" data-currency="USD/CLP">
                            <strong>USD/CLP</strong>
                            <small class="d-block text-muted">Dollar/Peso</small>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-info w-100 currency-btn" data-currency="USD/COP">
                            <strong>USD/COP</strong>
                            <small class="d-block text-muted">Dollar/Peso</small>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-info w-100 currency-btn" data-currency="USD/EGP">
                            <strong>USD/EGP</strong>
                            <small class="d-block text-muted">Dollar/Pound</small>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-info w-100 currency-btn" data-currency="USD/IDR">
                            <strong>USD/IDR</strong>
                            <small class="d-block text-muted">Dollar/Rupiah</small>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-info w-100 currency-btn" data-currency="USD/MYR">
                            <strong>USD/MYR</strong>
                            <small class="d-block text-muted">Dollar/Ringgit</small>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-info w-100 currency-btn" data-currency="USD/PHP">
                            <strong>USD/PHP</strong>
                            <small class="d-block text-muted">Dollar/Peso</small>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-info w-100 currency-btn" data-currency="USD/PKR">
                            <strong>USD/PKR</strong>
                            <small class="d-block text-muted">Dollar/Rupee</small>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-info w-100 currency-btn" data-currency="USD/VND">
                            <strong>USD/VND</strong>
                            <small class="d-block text-muted">Dollar/Dong</small>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-info w-100 currency-btn" data-currency="USD/BDT">
                            <strong>USD/BDT</strong>
                            <small class="d-block text-muted">Dollar/Taka</small>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-info w-100 currency-btn" data-currency="USD/DZD">
                            <strong>USD/DZD</strong>
                            <small class="d-block text-muted">Dollar/Dinar</small>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-info w-100 currency-btn" data-currency="LBP/USD">
                            <strong>LBP/USD</strong>
                            <small class="d-block text-muted">Pound/Dollar</small>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-info w-100 currency-btn" data-currency="MAD/USD">
                            <strong>MAD/USD</strong>
                            <small class="d-block text-muted">Dirham/Dollar</small>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-info w-100 currency-btn" data-currency="TND/USD">
                            <strong>TND/USD</strong>
                            <small class="d-block text-muted">Dinar/Dollar</small>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-info w-100 currency-btn" data-currency="UAH/USD">
                            <strong>UAH/USD</strong>
                            <small class="d-block text-muted">Hryvnia/Dollar</small>
                        </button>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <button class="btn btn-outline-info w-100 currency-btn" data-currency="YER/USD">
                            <strong>YER/USD</strong>
                            <small class="d-block text-muted">Rial/Dollar</small>
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
                        <button class="btn btn-outline-info w-100 timeframe-btn" data-timeframe="3m">
                            <strong>3 минуты</strong>
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-outline-info w-100 timeframe-btn" data-timeframe="4m">
                            <strong>4 минуты</strong>
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
    /* Отключение анимаций и эффектов только для контейнера сигналов */
    .container-fluid .main-card *:not(.dropdown-menu):not(.dropdown-item):not(.navbar *) {
        transition: none !important;
        animation: none !important;
        transform: none !important;
    }

    .main-card .btn:hover, .main-card .btn:focus, .main-card .btn:active,
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
        background: linear-gradient(135deg, #00c851 0%, #00a040 100%) !important;
        color: white !important;
        opacity: 0.7;
        cursor: not-allowed;
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

    .direction-icon.вверх {
        background: rgba(0,200,81,0.3);
        color: #00c851;
    }

    .direction-icon.вниз {
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
        cursor: pointer;
        transition: all 0.2s ease;
        border-radius: 8px;
        padding: 5px 10px;
        position: relative;
        display: inline-block;
    }

    .signal-currency:hover {
        transform: scale(1.02);
    }

    .signal-currency::after {
        content: "📋";
        position: absolute;
        right: -25px;
        top: 50%;
        transform: translateY(-50%);
        opacity: 0;
        transition: opacity 0.2s ease;
        font-size: 0.8rem;
    }

    .signal-currency:hover::after {
        opacity: 1;
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
            this.lastSignal = null;
            this.isSearching = false; // Добавляем флаг для предотвращения множественных запросов
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

            // Поиск валютных пар
            const currencySearch = document.getElementById('currencySearch');
            if (currencySearch) {
                currencySearch.addEventListener('input', (e) => {
                    this.filterCurrencies(e.target.value);
                });
            }

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

        filterCurrencies(searchTerm) {
            const term = searchTerm.toLowerCase();
            const currencyButtons = document.querySelectorAll('.currency-btn');
            
            currencyButtons.forEach(btn => {
                const currency = btn.dataset.currency.toLowerCase();
                const currencyText = btn.textContent.toLowerCase();
                const parentDiv = btn.closest('.col-md-6');
                
                if (currency.includes(term) || currencyText.includes(term)) {
                    parentDiv.style.display = '';
                } else {
                    parentDiv.style.display = 'none';
                }
            });

            // Показать/скрыть заголовки категорий
            const categories = document.querySelectorAll('#currencyModal h6');
            categories.forEach(category => {
                const nextDiv = category.nextElementSibling;
                if (nextDiv && nextDiv.classList.contains('row')) {
                    const visibleButtons = nextDiv.querySelectorAll('.col-md-6:not([style*="display: none"])');
                    if (visibleButtons.length === 0) {
                        category.style.display = 'none';
                        nextDiv.style.display = 'none';
                    } else {
                        category.style.display = '';
                        nextDiv.style.display = '';
                    }
                }
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
            // Предотвратить множественные запросы
            if (this.isSearching) {
                return;
            }
            
            const findBtn = document.getElementById('findSignalBtn');
            const loadingAnimation = document.getElementById('loadingAnimation');
            const signalResult = document.getElementById('signalResult');

            // Установить флаг поиска
            this.isSearching = true;
            
            // Очистить предыдущий сигнал
            this.lastSignal = null;

            // Показать загрузку
            findBtn.disabled = true;
            findBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span class="btn-text">Поиск сигнала...</span>';
            findBtn.style.display = 'inline-block'; // Убедиться что кнопка видна
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
                
                // Принудительно убрать фокус с кнопки
                findBtn.blur();
                
                // Сбросить все классы состояний
                findBtn.classList.remove('active', 'focus');
                findBtn.removeAttribute('data-bs-toggle');
                
                // Сбросить флаг поиска
                this.isSearching = false;
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
                const intervalTime = 50; // Уменьшаем интервал для более плавной анимации
                
                const interval = setInterval(() => {
                    elapsed += intervalTime;
                    const progress = Math.min((elapsed / duration) * 100, 100);
                    
                    if (progressBar && progressBar.parentElement) {
                        progressBar.style.width = progress + '%';
                        // Добавляем transition для плавности
                        progressBar.style.transition = 'width 0.05s ease-out';
                    }
                    
                    if (countdown && countdown.parentElement) {
                        const remaining = Math.max(Math.ceil((duration - elapsed) / 1000), 0);
                        countdown.textContent = `${remaining}s`;
                    }
                    
                    if (elapsed >= duration) {
                        clearInterval(interval);
                        
                        // Убедиться что прогресс-бар заполнен до 100%
                        if (progressBar && progressBar.parentElement) {
                            progressBar.style.width = '100%';
                        }
                        
                        // Удалить элементы через небольшую задержку
                        setTimeout(() => {
                            if (progressBar && progressBar.parentElement) {
                                progressBar.parentElement.remove();
                            }
                            if (countdown && countdown.parentElement) {
                                countdown.remove();
                            }
                        }, 100);
                        
                        resolve();
                    }
                }, intervalTime);
            });
        }

        createProgressBar() {
            const signalCard = document.querySelector('.signal-card');
            if (!signalCard) return null;

            // Удалить существующий прогресс-бар если есть
            const existingProgress = signalCard.querySelector('.progress-container');
            if (existingProgress) {
                existingProgress.remove();
            }

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
                z-index: 10;
            `;

            const progressBar = document.createElement('div');
            progressBar.className = 'progress-bar';
            progressBar.style.cssText = `
                height: 100%;
                width: 0%;
                background: linear-gradient(90deg, #00c851, #00ff66);
                box-shadow: 0 0 10px rgba(0,255,102,0.5);
                transition: width 0.1s ease-out;
                border-radius: 0 0 25px 25px;
                transform: translateZ(0);
            `;

            progressContainer.appendChild(progressBar);
            signalCard.appendChild(progressContainer);

            return progressBar;
        }

        createCountdown(duration) {
            const signalCard = document.querySelector('.signal-card');
            if (!signalCard) return null;

            // Удалить существующий счетчик если есть
            const existingCountdown = signalCard.querySelector('.countdown');
            if (existingCountdown) {
                existingCountdown.remove();
            }

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
                z-index: 10;
                user-select: none;
            `;

            signalCard.appendChild(countdown);
            return countdown;
        }

        generateSignal() {
            // По умолчанию используем случайные значения для всего рынка
            const currency = this.selectedCurrency || this.currencies[Math.floor(Math.random() * this.currencies.length)];
            const timeframe = this.selectedTimeframe || this.timeframes[Math.floor(Math.random() * this.timeframes.length)];
            const direction = Math.random() > 0.5 ? 'ВВЕРХ' : 'ВНИЗ';

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
            directionIcon.querySelector('i').className = `fas fa-arrow-${signal.direction === 'ВВЕРХ' ? 'up' : 'down'}`;
            directionText.textContent = signal.direction;

            // Информация
            signalCurrency.textContent = signal.currency;
            signalTime.textContent = signal.timeframe;
            signalProbability.textContent = `${signal.probability}%`;
            signalEntryPrice.textContent = signal.entryPrice;

            // Перепривязываем обработчик клика для копирования валюты
            this.setupCurrencyCopyListener();
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
                '30s': 30000, '1m': 60000, '2m': 120000, '3m': 180000, '4m': 240000, '5m': 300000,
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

            // Очистить поиск валют при закрытии модального окна
            if (modalId === 'currencyModal') {
                const currencySearch = document.getElementById('currencySearch');
                if (currencySearch) {
                    currencySearch.value = '';
                    this.filterCurrencies(''); // Показать все валютные пары
                }
            }
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