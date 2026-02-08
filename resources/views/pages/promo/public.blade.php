@extends('layouts.main')

@section('title', __('Статистика промокода') . ' ' . $promo->code)

@push('meta')
<meta name="robots" content="noindex, nofollow">
@endpush

@section('content')
<div class="inner">
    <div class="container">
        <div class="promo-public-page">

            {{-- Заголовок --}}
            <div class="promo-header">
                <div class="promo-header__badge">
                    <span class="promo-header__code">{{ strtoupper($promo->code) }}</span>
                </div>
                @if($promo->title && $promo->title !== $promo->code)
                    <div class="promo-header__title">{{ $promo->title }}</div>
                @endif
            </div>

            {{-- Основная статистика --}}
            <div class="promo-stats-grid">
                <div class="promo-stat-card promo-stat-card--primary">
                    <div class="promo-stat-card__icon">
                        <i class="fa-solid fa-ruble-sign"></i>
                    </div>
                    <div class="promo-stat-card__content">
                        <div class="promo-stat-card__value">{{ number_format($donateStats['total_amount'], 0, ',', ' ') }} ₽</div>
                        <div class="promo-stat-card__label">{{ __('Сумма донатов') }}</div>
                    </div>
                </div>

                <div class="promo-stat-card">
                    <div class="promo-stat-card__icon">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                    <div class="promo-stat-card__content">
                        <div class="promo-stat-card__value">{{ $donateStats['total_count'] }}</div>
                        <div class="promo-stat-card__label">{{ __('Количество донатов') }}</div>
                    </div>
                </div>

                <div class="promo-stat-card">
                    <div class="promo-stat-card__icon">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <div class="promo-stat-card__content">
                        <div class="promo-stat-card__value">{{ $totalActivations }}</div>
                        <div class="promo-stat-card__label">{{ __('Всего активаций') }}</div>
                    </div>
                </div>

                <div class="promo-stat-card">
                    <div class="promo-stat-card__icon">
                        <i class="fa-solid fa-gamepad"></i>
                    </div>
                    <div class="promo-stat-card__content">
                        <div class="promo-stat-card__value">{{ $serverActivations }}</div>
                        <div class="promo-stat-card__label">{{ __('С сервера') }}</div>
                    </div>
                </div>
            </div>

            {{-- Период --}}
            @if($donateStats['first_donation_at'])
            <div class="promo-period">
                <span class="promo-period__label">{{ __('Период') }}:</span>
                <span class="promo-period__value">
                    {{ \Carbon\Carbon::parse($donateStats['first_donation_at'])->format('d.m.Y') }}
                    —
                    {{ \Carbon\Carbon::parse($donateStats['last_donation_at'])->format('d.m.Y') }}
                </span>
            </div>
            @endif

            {{-- График по дням --}}
            @if($dailyStats->count() > 0)
            <div class="promo-section">
                <div class="promo-section__header">
                    <h2 class="promo-section__title">{{ __('Донаты за 30 дней') }}</h2>
                </div>
                <div class="promo-table-wrap">
                    <table class="promo-table">
                        <thead>
                            <tr>
                                <th>{{ __('Дата') }}</th>
                                <th>{{ __('Кол-во') }}</th>
                                <th>{{ __('Сумма') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dailyStats as $day)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($day->date)->format('d.m.Y') }}</td>
                                <td>{{ $day->count }}</td>
                                <td class="promo-table__amount">{{ number_format($day->total, 0, ',', ' ') }} ₽</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            {{-- Таблица донатов --}}
            @if($donations instanceof \Illuminate\Pagination\LengthAwarePaginator && $donations->count() > 0)
            <div class="promo-section">
                <div class="promo-section__header">
                    <h2 class="promo-section__title">{{ __('Последние донаты') }}</h2>
                </div>
                <div class="promo-table-wrap">
                    <table class="promo-table">
                        <thead>
                            <tr>
                                <th>{{ __('Дата') }}</th>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Сумма') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($donations as $donation)
                            <tr>
                                <td>{{ $donation->created_at->format('d.m.Y H:i') }}</td>
                                <td class="promo-table__id">
                                    @if($donation->user_id)
                                        #{{ $donation->user_id }}
                                    @else
                                        {{ substr($donation->steam_id ?? '', -6) }}
                                    @endif
                                </td>
                                <td class="promo-table__amount">{{ number_format($donation->amount, 0, ',', ' ') }} ₽</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($donations->hasPages())
                <div class="promo-pagination pagination">
                    {{ $donations->links('layouts.pagination.promo-public') }}
                </div>
                @endif
            </div>
            @endif

            @if($totalActivations == 0 && $donateStats['total_count'] == 0)
            <div class="promo-empty">
                <div class="promo-empty__icon"><i class="fa-solid fa-chart-simple"></i></div>
                <div class="promo-empty__text">{{ __('Пока нет данных') }}</div>
            </div>
            @endif

        </div>
    </div>
</div>

<style>
.promo-public-page {
    padding: 60px 0;
    min-height: 60vh;
}

/* Header */
.promo-header {
    text-align: center;
    margin-bottom: 50px;
}

.promo-header__badge {
    display: inline-block;
    background: linear-gradient(135deg, #cd412b 0%, #a33527 100%);
    padding: 16px 40px;
    border-radius: 4px;
    margin-bottom: 16px;
    box-shadow: 0 4px 20px rgba(205, 65, 43, 0.4);
}

.promo-header__code {
    font-family: 'Rust', sans-serif;
    font-size: 32px;
    color: #fff;
    letter-spacing: 3px;
}

.promo-header__title {
    font-family: 'Stem', sans-serif;
    font-weight: 500;
    font-size: 18px;
    color: rgba(255,255,255,0.7);
    margin-top: 8px;
}

/* Stats Grid */
.promo-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}

.promo-stat-card {
    background: rgba(30, 30, 30, 0.8);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 4px;
    padding: 24px;
    display: flex;
    align-items: center;
    gap: 20px;
    transition: all 0.3s ease;
}

.promo-stat-card:hover {
    border-color: rgba(205, 65, 43, 0.5);
    transform: translateY(-2px);
}

.promo-stat-card--primary {
    background: linear-gradient(135deg, rgba(205, 65, 43, 0.2) 0%, rgba(163, 53, 39, 0.15) 100%);
    border-color: rgba(205, 65, 43, 0.4);
}

.promo-stat-card__icon {
    width: 56px;
    height: 56px;
    background: rgba(205, 65, 43, 0.15);
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #cd412b;
    font-size: 24px;
    flex-shrink: 0;
}

.promo-stat-card__value {
    font-family: 'Stem', sans-serif;
    font-size: 28px;
    color: #fff;
    line-height: 1.2;
}

.promo-stat-card__label {
    font-family: 'Stem', sans-serif;
    font-weight: 500;
    font-size: 13px;
    color: rgba(255,255,255,0.5);
    margin-top: 4px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Period */
.promo-period {
    text-align: center;
    margin-bottom: 40px;
    padding: 16px;
    background: rgba(30, 30, 30, 0.5);
    border-radius: 4px;
}

.promo-period__label {
    font-family: 'Stem', sans-serif;
    font-weight: 500;
    color: rgba(255,255,255,0.5);
    margin-right: 8px;
}

.promo-period__value {
    font-family: 'Stem', sans-serif;
    font-weight: bold;
    color: #fff;
}

/* Section */
.promo-section {
    background: rgba(30, 30, 30, 0.8);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 4px;
    margin-bottom: 30px;
    overflow: hidden;
}

.promo-section__header {
    padding: 20px 24px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

.promo-section__title {
    font-family: 'Rust', sans-serif;
    font-size: 18px;
    color: #fff;
    margin: 0;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Table */
.promo-table-wrap {
    overflow-x: auto;
}

.promo-table {
    width: 100%;
    border-collapse: collapse;
}

.promo-table th,
.promo-table td {
    padding: 14px 24px;
    text-align: left;
    border-bottom: 1px solid rgba(255,255,255,0.05);
}

.promo-table th {
    font-family: 'Stem', sans-serif;
    font-weight: 500;
    font-size: 12px;
    color: rgba(255,255,255,0.4);
    text-transform: uppercase;
    letter-spacing: 1px;
}

.promo-table td {
    font-family: 'Stem', sans-serif;
    font-size: 14px;
    color: rgba(255,255,255,0.8);
}

.promo-table tbody tr:hover td {
    background: rgba(255,255,255,0.03);
}

.promo-table__amount {
    font-family: 'Stem', sans-serif;
    font-weight: bold;
    color: #4ade80 !important;
}

.promo-table__id {
    font-family: monospace;
    color: rgba(255,255,255,0.5) !important;
}

/* Pagination */
.promo-pagination {
    padding: 20px 24px;
    border-top: 1px solid rgba(255,255,255,0.1);
    display: flex;
    justify-content: center;
    gap: 8px;
}

.promo-pagination a {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 36px;
    height: 36px;
    padding: 0 12px;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 4px;
    color: rgba(255,255,255,0.7);
    text-decoration: none;
    font-family: 'Stem', sans-serif;
    font-size: 14px;
    transition: all 0.2s;
}

.promo-pagination a:hover {
    background: rgba(205, 65, 43, 0.2);
    border-color: rgba(205, 65, 43, 0.4);
    color: #fff;
}

.promo-pagination a.active {
    background: #cd412b;
    border-color: #cd412b;
    color: #fff;
}

.promo-pagination a.disabled {
    opacity: 0.3;
    cursor: not-allowed;
    pointer-events: none;
}

.promo-pagination a.dots {
    background: transparent;
    border-color: transparent;
}

/* Empty */
.promo-empty {
    text-align: center;
    padding: 80px 20px;
}

.promo-empty__icon {
    font-size: 64px;
    margin-bottom: 20px;
    color: rgba(255,255,255,0.3);
}

.promo-empty__text {
    font-family: 'Stem', sans-serif;
    font-weight: 500;
    font-size: 18px;
    color: rgba(255,255,255,0.4);
}

/* Responsive */
@media (max-width: 768px) {
    .promo-public-page {
        padding: 30px 0;
    }

    .promo-header__code {
        font-size: 24px;
    }

    .promo-stats-grid {
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .promo-stat-card {
        padding: 16px;
        gap: 12px;
    }

    .promo-stat-card__icon {
        width: 44px;
        height: 44px;
        font-size: 18px;
    }

    .promo-stat-card__value {
        font-size: 20px;
    }

    .promo-stat-card__label {
        font-size: 11px;
    }

    .promo-table th,
    .promo-table td {
        padding: 10px 12px;
        font-size: 13px;
    }
}

@media (max-width: 480px) {
    .promo-stats-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection
