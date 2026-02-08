@extends('layouts.main')

@section('content')
<div class="inner">
    <div class="container">
        <div class="promo-stats-page">
            {{-- SEO meta --}}
            @section('meta')
                <meta name="robots" content="noindex, nofollow">
            @endsection

            <div class="promo-stats-header">
                <h1>{{ __('Статистика промокода') }}</h1>
                <div class="promo-code-badge">
                    <span class="code">{{ $promo->code }}</span>
                </div>
                @if($promo->title && $promo->title !== $promo->code)
                    <p class="promo-title">{{ $promo->title }}</p>
                @endif
            </div>

            <div class="stats-grid">
                {{-- Карточка активаций --}}
                <div class="stat-card">
                    <div class="stat-icon">📊</div>
                    <div class="stat-content">
                        <div class="stat-value">{{ $totalActivations }}</div>
                        <div class="stat-label">{{ __('Всего активаций') }}</div>
                    </div>
                </div>

                {{-- Карточка с сервера --}}
                <div class="stat-card">
                    <div class="stat-icon">🎮</div>
                    <div class="stat-content">
                        <div class="stat-value">{{ $serverActivations }}</div>
                        <div class="stat-label">{{ __('С сервера') }}</div>
                    </div>
                </div>

                {{-- Карточка с сайта --}}
                <div class="stat-card">
                    <div class="stat-icon">🌐</div>
                    <div class="stat-content">
                        <div class="stat-value">{{ $siteActivations }}</div>
                        <div class="stat-label">{{ __('С сайта') }}</div>
                    </div>
                </div>

                {{-- Карточка донатов --}}
                <div class="stat-card highlight">
                    <div class="stat-icon">💰</div>
                    <div class="stat-content">
                        <div class="stat-value">{{ number_format($donateStats['total_amount'], 0, '.', ' ') }} ₽</div>
                        <div class="stat-label">{{ __('Сумма донатов') }}</div>
                    </div>
                </div>

                {{-- Количество донатов --}}
                <div class="stat-card">
                    <div class="stat-icon">🧾</div>
                    <div class="stat-content">
                        <div class="stat-value">{{ $donateStats['total_count'] }}</div>
                        <div class="stat-label">{{ __('Количество донатов') }}</div>
                    </div>
                </div>

                {{-- Период --}}
                @if($donateStats['first_donation_at'])
                <div class="stat-card">
                    <div class="stat-icon">📅</div>
                    <div class="stat-content">
                        <div class="stat-value-small">
                            {{ \Carbon\Carbon::parse($donateStats['first_donation_at'])->format('d.m.Y') }}
                            —
                            {{ \Carbon\Carbon::parse($donateStats['last_donation_at'])->format('d.m.Y') }}
                        </div>
                        <div class="stat-label">{{ __('Период') }}</div>
                    </div>
                </div>
                @endif
            </div>

            {{-- График по дням --}}
            @if($dailyStats->count() > 0)
            <div class="daily-stats-section">
                <h2>{{ __('Донаты за последние 30 дней') }}</h2>
                <div class="daily-stats-table">
                    <table>
                        <thead>
                            <tr>
                                <th>{{ __('Дата') }}</th>
                                <th>{{ __('Количество') }}</th>
                                <th>{{ __('Сумма') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dailyStats as $day)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($day->date)->format('d.m.Y') }}</td>
                                <td>{{ $day->count }}</td>
                                <td>{{ number_format($day->total, 0, '.', ' ') }} ₽</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            {{-- Таблица донатов --}}
            @if($donations->count() > 0)
            <div class="donations-section">
                <h2>{{ __('Последние донаты') }}</h2>
                <div class="donations-table">
                    <table>
                        <thead>
                            <tr>
                                <th>{{ __('Дата') }}</th>
                                <th>{{ __('User ID') }}</th>
                                <th>{{ __('Сумма') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($donations as $donation)
                            <tr>
                                <td>{{ $donation->created_at->format('d.m.Y H:i') }}</td>
                                <td>
                                    @if($donation->user_id)
                                        #{{ $donation->user_id }}
                                    @else
                                        {{ substr($donation->steam_id, 0, 8) }}...
                                    @endif
                                </td>
                                <td>{{ number_format($donation->amount, 0, '.', ' ') }} ₽</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Пагинация --}}
                <div class="pagination-wrapper">
                    {{ $donations->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.promo-stats-page {
    padding: 40px 0;
    min-height: 60vh;
}

.promo-stats-header {
    text-align: center;
    margin-bottom: 40px;
}

.promo-stats-header h1 {
    font-size: 28px;
    margin-bottom: 20px;
    color: #fff;
}

.promo-code-badge {
    display: inline-block;
    background: linear-gradient(135deg, #f5a623 0%, #f7931e 100%);
    padding: 12px 30px;
    border-radius: 8px;
    margin-bottom: 10px;
}

.promo-code-badge .code {
    font-size: 24px;
    font-weight: bold;
    color: #fff;
    text-transform: uppercase;
    letter-spacing: 2px;
}

.promo-title {
    color: #888;
    font-size: 16px;
    margin-top: 10px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}

.stat-card {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    padding: 24px;
    display: flex;
    align-items: center;
    gap: 16px;
    transition: transform 0.2s, border-color 0.2s;
}

.stat-card:hover {
    transform: translateY(-2px);
    border-color: rgba(245, 166, 35, 0.3);
}

.stat-card.highlight {
    background: linear-gradient(135deg, rgba(245, 166, 35, 0.15) 0%, rgba(247, 147, 30, 0.1) 100%);
    border-color: rgba(245, 166, 35, 0.3);
}

.stat-icon {
    font-size: 32px;
}

.stat-content {
    flex: 1;
}

.stat-value {
    font-size: 28px;
    font-weight: bold;
    color: #fff;
    line-height: 1.2;
}

.stat-value-small {
    font-size: 16px;
    font-weight: bold;
    color: #fff;
}

.stat-label {
    font-size: 14px;
    color: #888;
    margin-top: 4px;
}

.daily-stats-section,
.donations-section {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 30px;
}

.daily-stats-section h2,
.donations-section h2 {
    font-size: 20px;
    margin-bottom: 20px;
    color: #fff;
}

.daily-stats-table table,
.donations-table table {
    width: 100%;
    border-collapse: collapse;
}

.daily-stats-table th,
.daily-stats-table td,
.donations-table th,
.donations-table td {
    padding: 12px 16px;
    text-align: left;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.daily-stats-table th,
.donations-table th {
    color: #888;
    font-weight: 500;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.daily-stats-table td,
.donations-table td {
    color: #fff;
    font-size: 14px;
}

.daily-stats-table tr:hover td,
.donations-table tr:hover td {
    background: rgba(255, 255, 255, 0.03);
}

.pagination-wrapper {
    margin-top: 20px;
    display: flex;
    justify-content: center;
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr 1fr;
    }
    
    .stat-value {
        font-size: 22px;
    }
    
    .promo-code-badge .code {
        font-size: 18px;
    }
}

@media (max-width: 480px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection
