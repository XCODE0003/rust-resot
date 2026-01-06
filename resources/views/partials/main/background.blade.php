{{--
<li class="effect"><img src="/images/new/sparkles.png"></li>
<li class="effect2"><img src="/images/new/sparkles.png"></li>
--}}

<div class="header-background-shadow @if(url()->current() == route('shop.item.test')){{ 'spb—sp-inner' }}@endif" @if(url()->current() != route('index')) style="height: 350px;" @endif></div>
<ul class="header-background @if(url()->current() == route('shop.item.test')){{ 'spb—sp-inner' }}@endif" @if(url()->current() != route('index')) style="height: 350px;" @endif>

    @if(!empty(session('backgrounds')))
        @foreach(session('backgrounds') as $background)
            <li><span style="background-image: url(/storage/{{ $background['image'] }});"></span></li>
        @endforeach
    @else
        <li><span></span></li>
        <li><span></span></li>
        <li><span></span></li>
        <li><span></span></li>
        <li><span></span></li>
    @endif

</ul>