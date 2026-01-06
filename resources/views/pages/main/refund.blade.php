@extends('layouts.main')

@section('title', __('Политика возврата') . ' - ' . config('options.main_title_'.app()->getLocale(), '') )

@section('content')

    <div class="inner-header">{{ __('ПОЛИТИКА ВОЗВРАТА') }}</div>

    <div class="inner">
        <div class="container">
            <div class="terms">
                {!! config('options.refund_'.app()->getLocale(), 'term') !!}
            </div>
        </div>
    </div>

@endsection