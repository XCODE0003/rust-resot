@extends('layouts.dashlite')

@section('content')
    <div class="nk-wrap ">

        <div class="nk-content ">
            <div class="container wide-xl">
                <div class="nk-content-inner">

                    <div class="nk-content-body">
                        <div class="nk-content-wrap">
                            {{-- End Alert --}}
                            @yield('wrap')
                        </div>

                        @include('partials.cabinet.footer')
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.main')

@section('title', config('options.main_title_'.app()->getLocale(), '') )

@section('content')

    <!--background header-->
    @include('partials.main.background')
    <!--background header-->

    @include('partials.main.header')


    <!--header-->
    <section class="header">
        <div class="container">

            <!--logo-->

            <div class="header-logo"><div class="logo" style="transform: rotateY(0deg) rotateX(0deg);"><img src="/images/logo.png"></div></div>

            <!--end logo-->


            <!--news-->

            <div class="header-news swiper-container-horizontal">

                <div class="swiper-wrapper" style="transform: translate3d(0px, 0px, 0px);">

                    <a href="https://unsimple.com/" class="swiper-slide header-news-content swiper-slide-visible swiper-slide-active" style="width: 625px; margin-right: 30px;">
                        <div class="header-news-content-img"><img src="/images/1.jpg"></div>
                        <div class="header-news-content-text">
                            <span>August 25 2022</span>
                            <h1>Textline for the news, some more text here and much more right here too</h1>
                        </div>
                    </a>

                    <a href="https://unsimple.com/" class="swiper-slide header-news-content swiper-slide-visible swiper-slide-next" style="width: 625px; margin-right: 30px;">
                        <div class="header-news-content-img"><img src="/images/1.jpg"></div>
                        <div class="header-news-content-text">
                            <span>August 25 2022</span>
                            <h1>Textline for the news, some more text here and much more right here too</h1>
                        </div>
                    </a>

                    <a href="https://unsimple.com/" class="swiper-slide header-news-content" style="width: 625px; margin-right: 30px;">
                        <div class="header-news-content-img"><img src="/images/1.jpg"></div>
                        <div class="header-news-content-text">
                            <span>August 25 2022</span>
                            <h1>Textline for the news, some more text here and much more right here too</h1>
                        </div>
                    </a>

                    <a href="https://unsimple.com/" class="swiper-slide header-news-content" style="width: 625px; margin-right: 30px;">
                        <div class="header-news-content-img"><img src="/images/1.jpg"></div>
                        <div class="header-news-content-text">
                            <span>August 25 2022</span>
                            <h1>Textline for the news, some more text here and much more right here too</h1>
                        </div>
                    </a>

                    <a href="https://unsimple.com/" class="swiper-slide header-news-content" style="width: 625px; margin-right: 30px;">
                        <div class="header-news-content-img"><img src="/images/1.jpg"></div>
                        <div class="header-news-content-text">
                            <span>August 25 2022</span>
                            <h1>Textline for the news, some more text here and much more right here too</h1>
                        </div>
                    </a>
                </div>

                <!--controls-->

                <div class="swiper-button-prev header-news-control swiper-button-disabled" tabindex="0" role="button" aria-label="Previous slide" aria-disabled="true">
                    <span class="header-news-control-icon prev-icon"><i class="fa-solid fa-angles-left"></i></span><span class="prev"><p>Previous</p></span>
                </div>

                <div class="swiper-button-next header-news-control" tabindex="0" role="button" aria-label="Next slide" aria-disabled="false">
                    <span class="next"><p>Next</p></span><span class="header-news-control-icon next-icon"><i class="fa-solid fa-angles-right"></i></span>
                </div>

                <div class="swiper-pagination swiper-pagination-clickable swiper-pagination-bullets"><span class="swiper-pagination-bullet swiper-pagination-bullet-active" tabindex="0" role="button" aria-label="Go to slide 1"></span><span class="swiper-pagination-bullet" tabindex="0" role="button" aria-label="Go to slide 2"></span><span class="swiper-pagination-bullet" tabindex="0" role="button" aria-label="Go to slide 3"></span><span class="swiper-pagination-bullet" tabindex="0" role="button" aria-label="Go to slide 4"></span></div>

                <!--end controls-->

                <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span></div>

            <!--end news-->

        </div>

    </section>
    <!--end header-->

    <!--servers-->
    <section id="servers" class="servers">

        <div class="container">

            <div class="servers-title">Servers</div>

            <div class="servers-online">There are currently <span>8600</span> players in our servers</div>

            <div class="servers-list swiper-container-horizontal swiper-container-multirow">

                <div class="swiper-button-prev servers-list-control servers-list-prev swiper-button-disabled" tabindex="0" role="button" aria-label="Previous slide" aria-disabled="true">
                    <span class="servers-list-control-icon prev-icon"><i class="fa-solid fa-angles-left"></i></span><span class="prev"><p>Previous</p></span>
                </div>

                <div class="swiper-button-next servers-list-control servers-list-next swiper-button-disabled" tabindex="0" role="button" aria-label="Next slide" aria-disabled="true">
                    <span class="next"><p>Next</p></span><span class="servers-list-control-icon next-icon"><i class="fa-solid fa-angles-right"></i></span>
                </div>

                <div class="swiper-pagination servers-list-pagination swiper-pagination-clickable swiper-pagination-bullets"><span class="swiper-pagination-bullet swiper-pagination-bullet-active" tabindex="0" role="button" aria-label="Go to slide 1"></span></div>


                <div class="swiper-wrapper" style="width: 1330px; transform: translate3d(0px, 0px, 0px);">


                    <!--server content-->

                    <div class="swiper-slide servers-list-content swiper-slide-active" data-swiper-column="0" data-swiper-row="0" style="order: 0; width: 393.333px; margin-right: 50px;">
                        <div class="servers-list-content-name">
                            <div class="servers-list-content-name-text">EU Trio</div>
                            <div class="servers-list-content-name-wipe"><div><i class="fa-solid fa-exclamation"></i></div><p>Wiped <span>2 days ago</span></p></div>
                            <div class="servers-list-content-name-img"><img src="/images/1-2.jpg"></div>
                        </div>

                        <div class="servers-list-content-info">
                            <div class="servers-list-content-info-ip"><p>IP: <span>176.34.343.1</span></p></div>
                            <a href="https://unsimple.com/" class="servers-list-content-info-stats"><div>My stats</div></a>
                        </div>

                        <div class="servers-list-content-play">
                            <div class="servers-list-content-play-online"><p>Online: <span class="online">280</span> <span>/ 380</span></p></div>
                            <div class="servers-list-content-play-connect-shop">
                                <button class="connect"><div><span></span>Connect</div></button>
                                <a href="https://unsimple.com/" class="shop"><div><span></span><i class="fa-solid fa-cart-shopping"></i></div></a>
                            </div>
                        </div>
                    </div>


                    <!--end server content-->




                    <!--server content-->

                    <div class="swiper-slide servers-list-content swiper-slide-next" data-swiper-column="0" data-swiper-row="1" style="-webkit-box-ordinal-group: 3; order: 3; margin-top: 50px; width: 393.333px; margin-right: 50px;">
                        <div class="servers-list-content-name">
                            <div class="servers-list-content-name-text">EU Trio</div>
                            <div class="servers-list-content-name-wipe"><div><i class="fa-solid fa-exclamation"></i></div><p>Wiped <span>2 days ago</span></p></div>
                            <div class="servers-list-content-name-img"><img src="/images/1-2.jpg"></div>
                        </div>

                        <div class="servers-list-content-info">
                            <div class="servers-list-content-info-ip"><p>IP: <span>176.34.343.1</span></p></div>
                            <a href="https://unsimple.com/" class="servers-list-content-info-stats"><div>My stats</div></a>
                        </div>

                        <div class="servers-list-content-play">
                            <div class="servers-list-content-play-online"><p>Online: <span class="online">280</span> <span>/ 380</span></p></div>
                            <div class="servers-list-content-play-connect-shop">
                                <button class="connect"><div><span></span>Connect</div></button>
                                <a href="https://unsimple.com/" class="shop"><div><span></span><i class="fa-solid fa-cart-shopping"></i></div></a>
                            </div>
                        </div>
                    </div>


                    <!--end server content-->




                    <!--server content-->

                    <div class="swiper-slide servers-list-content" data-swiper-column="1" data-swiper-row="0" style="-webkit-box-ordinal-group: 1; order: 1; width: 393.333px; margin-right: 50px;">
                        <div class="servers-list-content-name">
                            <div class="servers-list-content-name-text">EU Monday</div>
                            <div class="servers-list-content-name-wipe"><div><i class="fa-solid fa-exclamation"></i></div><p>Wiped <span>2 days ago</span></p></div>
                            <div class="servers-list-content-name-img"><img src="/images/1.jpg"></div>
                        </div>

                        <div class="servers-list-content-info">
                            <div class="servers-list-content-info-ip"><p>IP: <span>176.34.343.1</span></p></div>
                            <a href="https://unsimple.com/" class="servers-list-content-info-stats"><div>My stats</div></a>
                        </div>

                        <div class="servers-list-content-play">
                            <div class="servers-list-content-play-online"><p>Online: <span class="online">280</span> <span>/ 380</span></p></div>
                            <div class="servers-list-content-play-connect-shop">
                                <button class="connect"><div><span></span>Connect</div></button>
                                <a href="https://unsimple.com/" class="shop"><div><span></span><i class="fa-solid fa-cart-shopping"></i></div></a>
                            </div>
                        </div>
                    </div>


                    <!--end server content-->





                    <!--server content-->

                    <div class="swiper-slide servers-list-content" data-swiper-column="1" data-swiper-row="1" style="-webkit-box-ordinal-group: 4; order: 4; margin-top: 50px; width: 393.333px; margin-right: 50px;">
                        <div class="servers-list-content-name">
                            <div class="servers-list-content-name-text">EU Trio</div>
                            <div class="servers-list-content-name-wipe"><div><i class="fa-solid fa-exclamation"></i></div><p>Wiped <span>2 days ago</span></p></div>
                            <div class="servers-list-content-name-img"><img src="/images/1-2.jpg"></div>
                        </div>

                        <div class="servers-list-content-info">
                            <div class="servers-list-content-info-ip"><p>IP: <span>176.34.343.1</span></p></div>
                            <a href="https://unsimple.com/" class="servers-list-content-info-stats"><div>My stats</div></a>
                        </div>

                        <div class="servers-list-content-play">
                            <div class="servers-list-content-play-online"><p>Online: <span class="online">280</span> <span>/ 380</span></p></div>
                            <div class="servers-list-content-play-connect-shop">
                                <button class="connect"><div><span></span>Connect</div></button>
                                <a href="https://unsimple.com/" class="shop"><div><span></span><i class="fa-solid fa-cart-shopping"></i></div></a>
                            </div>
                        </div>
                    </div>


                    <!--end server content-->





                    <!--server content-->

                    <div class="swiper-slide servers-list-content" data-swiper-column="2" data-swiper-row="0" style="-webkit-box-ordinal-group: 2; order: 2; width: 393.333px; margin-right: 50px;">
                        <div class="servers-list-content-name">
                            <div class="servers-list-content-name-text">EU Trio</div>
                            <div class="servers-list-content-name-wipe"><div><i class="fa-solid fa-exclamation"></i></div><p>Wiped <span>2 days ago</span></p></div>
                            <div class="servers-list-content-name-img"><img src="/images/1-2.jpg"></div>
                        </div>

                        <div class="servers-list-content-info">
                            <div class="servers-list-content-info-ip"><p>IP: <span>176.34.343.1</span></p></div>
                            <a href="https://unsimple.com/" class="servers-list-content-info-stats"><div>My stats</div></a>
                        </div>

                        <div class="servers-list-content-play">
                            <div class="servers-list-content-play-online"><p>Online: <span class="online">280</span> <span>/ 380</span></p></div>
                            <div class="servers-list-content-play-connect-shop">
                                <button class="connect"><div><span></span>Connect</div></button>
                                <a href="https://unsimple.com/" class="shop"><div><span></span><i class="fa-solid fa-cart-shopping"></i></div></a>
                            </div>
                        </div>
                    </div>


                    <!--end server content-->





                    <!--server content-->

                    <div class="swiper-slide servers-list-content" data-swiper-column="2" data-swiper-row="1" style="-webkit-box-ordinal-group: 5; order: 5; margin-top: 50px; width: 393.333px; margin-right: 50px;">
                        <div class="coming-soon">
                            <div class="coming-soon-icon"><i><i class="fa-solid fa-bullhorn"></i></i></div>
                            <div class="coming-soon-text">A new server it's coming soon...</div>
                            <a href="https://unsimple.com/">Learn more</a>
                        </div>
                    </div>


                    <!--end server content-->


                </div>
                <span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span></div>
        </div>
    </section>
    <!--end server-->


    @include('partials.main.footer')

@endsection