@extends('backend.layouts.dashlite')

@section('title', __('Панель управления'))

@section('wrap')

    @if(!auth()->user()->hasVerifiedEmail())
        <div class="alert alert-warning">
            <div class="alert-cta flex-wrap flex-md-nowrap">
                <div class="alert-text">
                    <p>{{ __('Вы не подтвердили Ваш E-Mail адрес.') }}</p>
                </div>
                <ul class="alert-actions gx-3 mt-3 mb-1 my-md-0">
                    <li class="order-md-last">
                        <a href="{{ route('verification.notice') }}" class="btn btn-sm btn-warning">{{ __('Подтвердить') }}</a>
                    </li>
                </ul>
            </div>
        </div>
    @endif

@endsection

@section('body')


    <body class="nk-body bg-lighter npc-general has-sidebar ">
    <div class="nk-app-root">
        <!-- main @s -->
        <div class="nk-main ">
            <!-- sidebar @s -->
            <div class="nk-sidebar nk-sidebar-fixed is-dark " data-content="sidebarMenu">
                <div class="nk-sidebar-element nk-sidebar-head">
                    <div class="nk-sidebar-brand">
                        <a href="{{ route('backend') }}" class="logo-link nk-sidebar-logo">
                            <img class="logo-light logo-img" src="/assets/images/logo-dark.png" srcset="/assets/images/logo-dark.png 2x" alt="logo">
                            <img class="logo-dark logo-img" src="/assets/images/logo-dark.png" srcset="/assets/images/logo-dark.png 2x" alt="logo-dark">
                        </a>
                    </div>
                    <div class="nk-menu-trigger mr-n2">
                        <a href="#" class="nk-nav-toggle nk-quick-nav-icon d-xl-none" data-target="sidebarMenu"><em class="icon ni ni-arrow-left"></em></a>
                    </div>
                </div>

                @include('backend.partials.main-menu')

            </div>
            <!-- sidebar @e -->
            <!-- wrap @s -->
            <div class="nk-wrap ">
                <!-- main header @s -->
                <div class="nk-header nk-header-fixed is-light">
                    <div class="container-fluid">
                        <div class="nk-header-wrap">
                            <div class="nk-menu-trigger d-xl-none ml-n1">
                                <a href="#" class="nk-nav-toggle nk-quick-nav-icon" data-target="sidebarMenu"><em class="icon ni ni-menu"></em></a>
                            </div>
                            <div class="nk-header-brand d-xl-none">
                                <a href="{{ route('backend') }}" class="logo-link">
                                    <img class="logo-light logo-img" src="/assets/images/logo-dark.png" srcset="/assets/images/logo-dark.png 2x" alt="logo">
                                    <img class="logo-dark logo-img" src="/assets/images/logo-dark.png" srcset="/assets/images/logo-dark.png 2x" alt="logo-dark">
                                    <span class="nio-version">General</span>
                                </a>
                            </div><!-- .nk-header-brand -->
                            <div class="nk-header-news d-none d-xl-block">
                                <div class="nk-news-list">

                                        <div class="nk-news-text">
                                            @include('backend.partials.alerts')
                                        </div>

                                </div>
                            </div><!-- .nk-header-news -->
                            <div class="nk-header-tools">

                                <ul class="nk-quick-nav">

                                @include('backend.partials.user-dropdown')

                                <!-- .dropdown -->

                                {{-- @include('backend.partials.notification-dropdown') --}}

                                <!-- .dropdown -->

                                    <li class="langs">
                                        @include('backend.partials.header-languages')
                                    </li>

                                </ul><!-- .nk-quick-nav -->
                            </div><!-- .nk-header-tools -->
                        </div><!-- .nk-header-wrap -->
                    </div><!-- .container-fliud -->
                </div>
                <!-- main header @e -->

                <!-- content @s -->
                <div class="nk-content ">
                    <div class="container-fluid">
                        <div class="nk-content-inner">
                            <div class="nk-content-body">
                                <div class="nk-block-head nk-block-head-sm">
                                    <div class="nk-block-between">
                                        <div class="nk-block-head-content">
                                            <h3 class="nk-block-title page-title">@yield('headerTitle')</h3>
                                            <div class="nk-block-des text-soft">
                                                <p>@yield('headerDesc')</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                    @yield('wrap')

                            </div>
                        </div>
                    </div>
                </div>
                <!-- content @e -->



                <!-- footer @s -->
                <div class="nk-footer">
                    <div class="container-fluid">
                        <div class="nk-footer-wrap">
                            <div class="nk-footer-copyright"> &copy; {{ date('Y') }} <a href="https://wizardcp.com" title="WizardCP — Control panel for magical game projects" target="_blank" rel="dofollow">WizardCP</a> by <a href="https://unsimpleworld.com" title="Website development / Разработка сайта — Unsimple World" target="_blank" rel="dofollow">Unsimple World</a>
                            </div>

                            <div class="btn-container">
                                <label class="switch btn-color-mode-switch">
                                    <input type="checkbox" name="color_mode" id="color_mode" @if(session()->has('theme') && session()->get('theme') == 'dark') checked @endif value="1">
                                    <label for="color_mode" data-on="Dark" data-off="Light" class="btn-color-mode-switch-inner"></label>
                                </label>
                            </div>

                            <div class="nk-footer-links">
                                <ul class="nav nav-sm">
                                    <li class="nav-item"><a class="nav-link" href="https://wizardcp.com" title="WizardCP — Control panel for magical game projects"  target="_blank" rel="dofollow"><img src="/assets/img/wizardcp-logo-light.png"/></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- footer @e -->
            </div>
            <!-- wrap @e -->
        </div>
        <!-- main @e -->
    </div>
    <!-- app-root @e -->

    </body>

    <script>
        $(document).ready(function() {
            $("#color_mode").on("change", function () {
                if($(this).prop("checked") == true){
                    //window.location.href = '{{ route('settheme', 'dark') }}';
                    $.get('{{ route('settheme', 'dark') }}');
                    $('link[href="/assets/css/theme.css"]').attr('href', '/assets/css/backend-dark.css?ver=2');
                }
                else if($(this).prop("checked") == false){
                    //window.location.href = '{{ route('settheme', 'light') }}';
                    $.get('{{ route('settheme', 'light') }}');
                    $('link[href="/assets/css/backend-dark.css?ver=2"]').attr('href', '/assets/css/theme.css');
                }
            })
        });
    </script>

@endsection