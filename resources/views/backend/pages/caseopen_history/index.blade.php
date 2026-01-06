@extends('backend.layouts.backend')

@section('title', __('Панель управления') . ' - ' . __('История Бесплатных открытий кейсов'))
@section('headerTitle', __('История Бесплатных открытий кейсов'))
@section('headerDesc', __('Список открытий.'))

@php
    $title = "title_" . app()->getLocale();
    $name = "name_" . app()->getLocale();
@endphp

@section('wrap')

    <!-- .nk-block -->
    <div class="nk-block">
        <div class="row g-gs">
            <div class="col-12">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-title-group" style="justify-content: flex-end;">
                            <div class="card-tools d-none d-md-inline">
                                <form method="GET">
                                    <div class="form-control-wrap">
                                        <div class="form-icon form-icon-left">
                                            <em class="icon ni ni-search"></em>
                                        </div>
                                        <input type="text" class="form-control" name="search" value="{{ request()->query('search') }}" placeholder="{{ __('Поиск') }}...">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="card-inner p-0 border-top">
                        <div class="nk-tb-list nk-tb-orders">
                            @foreach($histories as $history)
                                <div class="nk-tb-item">
                                    <div class="nk-tb-col">
                                        <span class="tb-lead">
                                            {{ $history->user->name }}
                                        </span>
                                    </div>
                                    <div class="nk-tb-col tb-col-md" style="display: flex !important;align-items: center;">
                                        <span class="tb-lead">
                                            @if(isset($history->getitem()->image_url) && isset($history->getitem()->$name))
                                                <img src="{{ $history->getitem()->image_url }}" alt="{{ $history->getitem()->$name }}" style="width: 50px;margin-right: 25px;">
                                            @endif
                                        </span>
                                        <span class="tb-lead">
                                            @if(isset($history->getitem()->$name))
                                                {{ $history->getitem()->$name }}
                                            @endif
                                        </span>
                                    </div>
                                    <div class="nk-tb-col tb-col-md">
                                        <span class="tb-sub">
                                            x{{ $history->item_amount }}
                                        </span>
                                    </div>

                                    <div class="nk-tb-col tb-col-md">
                                        <span class="tb-sub">
                                            {{ $history->case->$title }}
                                        </span>
                                    </div>
                                    <div class="nk-tb-col tb-col-md">
                                        <span class="tb-sub">
                                            {{ date('d/m/Y H:i', strtotime($history->date)) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="card-inner">
                        {{ $histories->links('backend.layouts.pagination.main') }}
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- .nk-block -->
@endsection
