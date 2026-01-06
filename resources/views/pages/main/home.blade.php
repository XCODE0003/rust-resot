@extends('layouts.main')

@section('title', config('options.main_title_'.app()->getLocale(), '') )

@section('content')

    <!--content-->
    <section class="header">
        <div class="container">

            <!--logo-->
            <!--end logo-->
        @include('partials.main.header-logo')

            <!--news-->
            <x-main.news />
            <!--end news-->

        </div>
    </section>
    <!--end content-->

    <!--servers-->
    <x-main.servers />
    <!--end server-->

@endsection