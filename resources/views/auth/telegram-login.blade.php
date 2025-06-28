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
                    
                    <div id="telegram-login-widget" class="d-flex justify-content-center">
                        <!-- Telegram Login Widget будет загружен здесь -->
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

<script async src="https://telegram.org/js/telegram-widget.js?22" 
        data-telegram-login="{{ config('services.telegram.bot_username') }}" 
        data-size="large" 
        data-auth-url="{{ route('telegram.auth') }}" 
        data-request-access="write">
</script>

<style>
    .card {
        margin-top: 50px;
    }
    
    #telegram-login-widget {
        min-height: 50px;
    }
</style>
@endsection 