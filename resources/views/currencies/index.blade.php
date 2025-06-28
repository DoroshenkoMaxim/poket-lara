@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Валютные пары PocketOption</h1>
            
            <!-- Статистика -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">{{ $stats['total'] }}</h5>
                            <p class="card-text">Всего валют</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">{{ $stats['active'] }}</h5>
                            <p class="card-text">Активных</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">{{ $stats['otc'] }}</h5>
                            <p class="card-text">OTC валют</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h6 class="card-title">{{ $stats['last_update'] ? $stats['last_update']->format('d.m.Y H:i') : 'Никогда' }}</h6>
                            <p class="card-text">Последнее обновление</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Кнопки управления -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <button id="updateCurrencies" class="btn btn-primary">Обновить валюты</button>
                    <button id="parseNow" class="btn btn-info">Парсинг сейчас</button>
                    <a href="{{ route('currencies.best') }}" class="btn btn-success">Лучшие валюты</a>
                </div>
            </div>
            
            <!-- Фильтры -->
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('currencies.index') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="search">Поиск:</label>
                                <input type="text" class="form-control" name="search" id="search" 
                                       value="{{ request('search') }}" placeholder="Название валюты">
                            </div>
                            <div class="col-md-2">
                                <label for="active">Статус:</label>
                                <select class="form-control" name="active" id="active">
                                    <option value="">Все</option>
                                    <option value="1" {{ request('active') === '1' ? 'selected' : '' }}>Активные</option>
                                    <option value="0" {{ request('active') === '0' ? 'selected' : '' }}>Неактивные</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="otc">Тип:</label>
                                <select class="form-control" name="otc" id="otc">
                                    <option value="">Все</option>
                                    <option value="1" {{ request('otc') === '1' ? 'selected' : '' }}>OTC</option>
                                    <option value="0" {{ request('otc') === '0' ? 'selected' : '' }}>Обычные</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="min_payout">Мин. выплата %:</label>
                                <input type="number" class="form-control" name="min_payout" id="min_payout" 
                                       value="{{ request('min_payout') }}" min="0" max="100">
                            </div>
                            <div class="col-md-2">
                                <label for="sort_by">Сортировка:</label>
                                <select class="form-control" name="sort_by" id="sort_by">
                                    <option value="payout" {{ request('sort_by') === 'payout' ? 'selected' : '' }}>По выплате</option>
                                    <option value="label" {{ request('sort_by') === 'label' ? 'selected' : '' }}>По названию</option>
                                    <option value="updated_at" {{ request('sort_by') === 'updated_at' ? 'selected' : '' }}>По дате</option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary form-control">Фильтр</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Таблица валют -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Символ</th>
                                    <th>Название</th>
                                    <th>Выплата</th>
                                    <th>Статус</th>
                                    <th>Тип</th>
                                    <th>Флаги</th>
                                    <th>Обновлено</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($currencies as $currency)
                                <tr class="{{ !$currency->is_active ? 'text-muted' : '' }}">
                                    <td><strong>{{ $currency->symbol }}</strong></td>
                                    <td>{{ $currency->label }}</td>
                                    <td>
                                        @if($currency->payout)
                                            <span class="badge badge-{{ $currency->payout >= 80 ? 'success' : ($currency->payout >= 60 ? 'warning' : 'secondary') }}">
                                                +{{ $currency->payout }}%
                                            </span>
                                        @else
                                            <span class="badge badge-light">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($currency->is_active)
                                            <span class="badge badge-success">Активна</span>
                                        @else
                                            <span class="badge badge-secondary">Неактивна</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($currency->is_otc)
                                            <span class="badge badge-info">OTC</span>
                                        @else
                                            <span class="badge badge-light">Обычная</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($currency->flags)
                                            @foreach($currency->flags as $flag)
                                                <span class="flag-icon flag-icon-{{ $flag }}" title="{{ strtoupper($flag) }}"></span>
                                            @endforeach
                                        @endif
                                    </td>
                                    <td>{{ $currency->last_updated ? $currency->last_updated->format('d.m.Y H:i') : '-' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">Валюты не найдены</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Пагинация -->
                    {{ $currencies->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Обновление валют
    document.getElementById('updateCurrencies').addEventListener('click', function() {
        this.disabled = true;
        this.innerHTML = 'Обновляем...';
        
        fetch('{{ route("currencies.update") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Успешно обновлено: ' + data.updated_count + ' валют');
                location.reload();
            } else {
                alert('Ошибка: ' + data.message);
            }
        })
        .catch(error => {
            alert('Ошибка сети: ' + error.message);
        })
        .finally(() => {
            this.disabled = false;
            this.innerHTML = 'Обновить валюты';
        });
    });
    
    // Парсинг сейчас
    document.getElementById('parseNow').addEventListener('click', function() {
        this.disabled = true;
        this.innerHTML = 'Парсим...';
        
        fetch('{{ route("currencies.parse-now") }}', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Парсинг выполнен: найдено ' + data.count + ' валют');
            } else {
                alert('Ошибка: ' + data.message);
            }
        })
        .catch(error => {
            alert('Ошибка сети: ' + error.message);
        })
        .finally(() => {
            this.disabled = false;
            this.innerHTML = 'Парсинг сейчас';
        });
    });
});
</script>
@endsection 