@extends('layouts.main')

@section('title', __('Помощь') . ' - ' . config('options.main_title_'.app()->getLocale(), '') )

@section('content')

  <div class="inner-header">{{ __('Помощь') }}</div>

  <div class="inner">
    <div class="container">
      <div class="servers" style="background: none; padding-top: 0;">

        <div class="servers-list">

          <div class="swiper-button-prev servers-list-control servers-list-prev">
            <span class="servers-list-control-icon prev-icon"><i class="fa-solid fa-angles-left"></i></span><span class="prev"><p>Previous</p></span>
          </div>

          <div class="swiper-button-next servers-list-control servers-list-next">
            <span class="next"><p>Next</p></span><span class="servers-list-control-icon next-icon"><i class="fa-solid fa-angles-right"></i></span>
          </div>

          <div class="swiper-pagination servers-list-pagination"></div>


          <div class="swiper-wrapper">


            <!--server content-->

            <div class="swiper-slide servers-list-content">
              <div class="servers-list-content-name">
                <div class="servers-list-content-name-text">EU Trio</div>
                <div class="servers-list-content-name-wipe"><div><i class="fa-solid fa-exclamation"></i></div><p>Wiped <span>2 days ago</span></p></div>
                <div class="servers-list-content-name-img"><img src="images/bg/1-2.jpg"></div>
              </div>

              <div class="servers-list-content-info">
                <div class="servers-list-content-info-ip"><p>IP: <span>176.34.343.1</span></p></div>
                <a href="/" class="servers-list-content-info-stats"><div>My stats</div></a>
              </div>

              <div class="servers-list-content-play">
                <div class="servers-list-content-play-online"><p>Online: <span class="online">280</span> <span>/ 380</span></p></div>
                <div class="servers-list-content-play-connect-shop">
                  <button class="connect"><div><span></span>Connect</div></button>
                  <a href="/" class="shop"><div><span></span><i class="fa-solid fa-cart-shopping"></i></div></a>
                </div>
              </div>
            </div>


            <!--end server content-->




            <!--server content-->

            <div class="swiper-slide servers-list-content">
              <div class="servers-list-content-name">
                <div class="servers-list-content-name-text">EU Trio</div>
                <div class="servers-list-content-name-wipe"><div><i class="fa-solid fa-exclamation"></i></div><p>Wiped <span>2 days ago</span></p></div>
                <div class="servers-list-content-name-img"><img src="images/bg/1-2.jpg"></div>
              </div>

              <div class="servers-list-content-info">
                <div class="servers-list-content-info-ip"><p>IP: <span>176.34.343.1</span></p></div>
                <a href="/" class="servers-list-content-info-stats"><div>My stats</div></a>
              </div>

              <div class="servers-list-content-play">
                <div class="servers-list-content-play-online"><p>Online: <span class="online">280</span> <span>/ 380</span></p></div>
                <div class="servers-list-content-play-connect-shop">
                  <button class="connect"><div><span></span>Connect</div></button>
                  <a href="/" class="shop"><div><span></span><i class="fa-solid fa-cart-shopping"></i></div></a>
                </div>
              </div>
            </div>


            <!--end server content-->




            <!--server content-->

            <div class="swiper-slide servers-list-content">
              <div class="servers-list-content-name">
                <div class="servers-list-content-name-text">EU Monday</div>
                <div class="servers-list-content-name-wipe"><div><i class="fa-solid fa-exclamation"></i></div><p>Wiped <span>2 days ago</span></p></div>
                <div class="servers-list-content-name-img"><img src="images/bg/1.jpg"></div>
              </div>

              <div class="servers-list-content-info">
                <div class="servers-list-content-info-ip"><p>IP: <span>176.34.343.1</span></p></div>
                <a href="/" class="servers-list-content-info-stats"><div>My stats</div></a>
              </div>

              <div class="servers-list-content-play">
                <div class="servers-list-content-play-online"><p>Online: <span class="online">280</span> <span>/ 380</span></p></div>
                <div class="servers-list-content-play-connect-shop">
                  <button class="connect"><div><span></span>Connect</div></button>
                  <a href="/" class="shop"><div><span></span><i class="fa-solid fa-cart-shopping"></i></div></a>
                </div>
              </div>
            </div>


            <!--end server content-->





            <!--server content-->

            <div class="swiper-slide servers-list-content">
              <div class="servers-list-content-name">
                <div class="servers-list-content-name-text">EU Trio</div>
                <div class="servers-list-content-name-wipe"><div><i class="fa-solid fa-exclamation"></i></div><p>Wiped <span>2 days ago</span></p></div>
                <div class="servers-list-content-name-img"><img src="images/bg/1-2.jpg"></div>
              </div>

              <div class="servers-list-content-info">
                <div class="servers-list-content-info-ip"><p>IP: <span>176.34.343.1</span></p></div>
                <a href="/" class="servers-list-content-info-stats"><div>My stats</div></a>
              </div>

              <div class="servers-list-content-play">
                <div class="servers-list-content-play-online"><p>Online: <span class="online">280</span> <span>/ 380</span></p></div>
                <div class="servers-list-content-play-connect-shop">
                  <button class="connect"><div><span></span>Connect</div></button>
                  <a href="/" class="shop"><div><span></span><i class="fa-solid fa-cart-shopping"></i></div></a>
                </div>
              </div>
            </div>


            <!--end server content-->





            <!--server content-->

            <div class="swiper-slide servers-list-content">
              <div class="servers-list-content-name">
                <div class="servers-list-content-name-text">EU Trio</div>
                <div class="servers-list-content-name-wipe"><div><i class="fa-solid fa-exclamation"></i></div><p>Wiped <span>2 days ago</span></p></div>
                <div class="servers-list-content-name-img"><img src="images/bg/1-2.jpg"></div>
              </div>

              <div class="servers-list-content-info">
                <div class="servers-list-content-info-ip"><p>IP: <span>176.34.343.1</span></p></div>
                <a href="/" class="servers-list-content-info-stats"><div>My stats</div></a>
              </div>

              <div class="servers-list-content-play">
                <div class="servers-list-content-play-online"><p>Online: <span class="online">280</span> <span>/ 380</span></p></div>
                <div class="servers-list-content-play-connect-shop">
                  <button class="connect"><div><span></span>Connect</div></button>
                  <a href="/" class="shop"><div><span></span><i class="fa-solid fa-cart-shopping"></i></div></a>
                </div>
              </div>
            </div>
            <!--end server content-->

            <!--server content-->

            <div class="swiper-slide servers-list-content">
              <div class="coming-soon">
                <div class="coming-soon-icon"><i><i class="fa-solid fa-bullhorn"></i></i></div>
                <div class="coming-soon-text">A new server it's coming soon...</div>
                <a href="/">Learn more</a>
              </div>
            </div>

            <!--end server content-->

          </div>
        </div>
      </div>
    </div>
  </div>

@endsection