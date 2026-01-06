@extends('layouts.main')

@section('title', __('Политика конфиденциальности') . ' - ' . config('options.main_title_'.app()->getLocale(), '') )

@section('content')

    <div class="inner-header">{{ __('ПОЛИТИКА КОНФИДЕНЦИАЛЬНОСТИ') }}</div>

    <div class="inner">
        <div class="container">
            <div class="terms">
                {!! config('options.policy_'.app()->getLocale(), 'term') !!}
            </div>
        </div>
    </div>

@endsection