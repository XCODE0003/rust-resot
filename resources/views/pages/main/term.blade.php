    @extends('layouts.main')

@section('title', __('Пользовательское соглашение') . ' - ' . config('options.main_title_'.app()->getLocale(), '') )

@section('content')

    <div class="inner-header">{{ __('ПОЛЬЗОВАТЕЛЬСКОЕ СОГЛАШЕНИЕ') }}</div>

    <div class="inner">
        <div class="container">
            <div class="terms">
                {!! config('options.term_'.app()->getLocale(), 'term') !!}
            </div>
        </div>
    </div>

@endsection