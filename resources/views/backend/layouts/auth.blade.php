@extends('backend.layouts.dashlite')
@section('body')

    <body class="nk-body bg-white npc-general ">
    <div class="nk-app-root grey-bg">
        <div class="nk-main">
            <div class="nk-wrap @@wrapClass">
                <div class="nk-content">

                    <div class="wide-md text-center m-auto h-100">
                        <div class="logo-link mb-5 mt-4" style="width: 30%;">
                                <img class="logo-light logo-img" src="/assets/images/logo.png" srcset="/assets/images/logo.png.png 2x" alt="logo">
                                <img class="logo-dark logo-img" src="/assets/images/logo-dark.png" srcset="/assets/images/logo-dark.png 2x" alt="logo-dark">
                        </div>
                        <div class="row g-gs">
                            <div class="col-lg-12">

                                <div class="nk-split-content nk-block-area nk-block-area-column nk-auth-container bg-white">
                                    <div class="nk-block nk-block-middle nk-auth-body">
                                        <div class="nk-block-head">
                                            <div class="nk-block-head-content">
                                                <h5 class="nk-block-title">@yield('title')</h5>
                                            </div>
                                        </div>
                                        @yield('form')
                                    </div>
                                    <div class="nk-block nk-auth-footer">
                                        <div class="mt-3">
                                            @include('backend.partials.footer-developer')
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/bundle.js') }}"></script>
    <script src="{{ mix('assets/js/scripts.js') }}"></script>
    @stack('scripts')
    </body>

@endsection
