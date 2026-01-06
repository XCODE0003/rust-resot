<div class="header-news container">
        <div class="swiper-wrapper">

            @foreach($articles as $article)
                @php $title = "title_" .app()->getLocale(); @endphp
                <a href="{{ route('news.show', $article->path) }}" class="swiper-slide header-news-content">
                    <div class="header-news-content-img"><img src="{{ $article->image_url }}"></div>
                    <div class="header-news-content-text">
                        <span>{{ getmonthname($article->updated_at->format('m')) }} {{ $article->updated_at->format('d Y') }}</span>
                        <h1>{!! $article->$title !!}</h1>
                    </div>
                </a>
            @endforeach

        </div>
        <!--controls-->
        <div class="swiper-button-prev header-news-control">
            <span class="header-news-control-icon prev-icon"><i class="fa-solid fa-angles-left"></i></span><span class="prev"><p>Previous</p></span>
        </div>
        <div class="swiper-button-next header-news-control">
            <span class="next"><p>Next</p></span><span class="header-news-control-icon next-icon"><i class="fa-solid fa-angles-right"></i></span>
        </div>
        <div class="swiper-pagination"></div>
        <!--end controls-->
    </div>
</div>

