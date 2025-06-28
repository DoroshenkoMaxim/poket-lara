@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-warning">
                <div class="card-header bg-warning text-dark text-center">
                    <h4 class="mb-0">
                        <i class="fas fa-exclamation-triangle"></i> Доступ к сигналам ограничен
                    </h4>
                </div>

                <div class="card-body text-center">
                    <div class="mb-4">
                        <i class="fas fa-lock fa-5x text-warning mb-3"></i>
                        <h5>Для доступа к торговым сигналам необходима регистрация</h5>
                    </div>

                    <div class="alert alert-info" role="alert">
                        <h6 class="alert-heading">
                            <i class="fas fa-info-circle"></i> Как получить доступ?
                        </h6>
                        <ol class="text-start mb-0">
                            <li>Обратитесь к нашему Telegram боту</li>
                            <li>Получите персональную ссылку для регистрации на PocketOption</li>
                            <li>Зарегистрируйтесь по этой ссылке</li>
                            <li>Получите автоматический доступ к сигналам на 24 часа</li>
                            <li>После истечения времени используйте виджет авторизации Telegram</li>
                        </ol>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="feature-card">
                                <i class="fas fa-chart-line fa-2x text-success mb-2"></i>
                                <h6>Точность сигналов</h6>
                                <p class="text-muted">До 90% успешных сделок</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="feature-card">
                                <i class="fas fa-clock fa-2x text-primary mb-2"></i>
                                <h6>Обновления</h6>
                                <p class="text-muted">Каждые 5 минут</p>
                            </div>
                        </div>
                    </div>

                    <div class="action-buttons">
                        <a href="https://t.me/{{ config('services.telegram.bot_username') }}" 
                           class="btn btn-primary btn-lg me-3" target="_blank">
                            <i class="fab fa-telegram-plane"></i> Открыть бота
                        </a>
                        
                        <a href="{{ route('login') }}" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-sign-in-alt"></i> Войти через Telegram
                        </a>
                    </div>

                    <div class="mt-4">
                        <small class="text-muted">
                            Уже зарегистрированы на PocketOption? 
                            <a href="{{ route('login') }}">Войдите через Telegram</a>
                        </small>
                    </div>
                </div>
            </div>

            <!-- Дополнительная информация -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-question-circle"></i> Часто задаваемые вопросы
                    </h5>
                </div>
                <div class="card-body">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faq1">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1">
                                    Как долго действует доступ к сигналам?
                                </button>
                            </h2>
                            <div id="collapse1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    После регистрации на PocketOption вы получаете автоматический доступ к сигналам на 24 часа. 
                                    После этого можете авторизоваться через виджет Telegram для постоянного доступа.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faq2">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2">
                                    Какие бонусы я получу при регистрации?
                                </button>
                            </h2>
                            <div id="collapse2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    При регистрации по нашей ссылке вы получите бонус WELCOME50 на ваш торговый счет 
                                    и бесплатный доступ к нашим торговым сигналам.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="faq3">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3">
                                    Насколько точны ваши сигналы?
                                </button>
                            </h2>
                            <div id="collapse3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Наши сигналы показывают точность от 75% до 90% в зависимости от рыночных условий. 
                                    Мы используем передовые алгоритмы анализа рынка для генерации сигналов.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .feature-card {
        padding: 20px;
        text-align: center;
        border-radius: 10px;
        background: #f8f9fa;
        margin-bottom: 20px;
        height: 140px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    
    .action-buttons {
        margin: 30px 0;
    }
    
    .accordion-button:not(.collapsed) {
        background-color: rgba(13, 110, 253, 0.1);
        border-color: rgba(13, 110, 253, 0.25);
    }
    
    .accordion-button:focus {
        box-shadow: none;
        border-color: rgba(13, 110, 253, 0.25);
    }
</style>
@endsection 