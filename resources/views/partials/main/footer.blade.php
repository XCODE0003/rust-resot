<section class="footer">

    <!--social-->
    @include('partials.main.footer-socials')
    <!--end social-->

    <!--copyright-->
    <div class="footer-copyright">

        @include('partials.main.footer-menu')



        <div class="footer-copyright-text">{{ config('options.copyright_description_'.app()->getLocale()) }}<br>Copyright {{ date('Y') }} Rust Resort</div>
        <div class="footer-copyright-text"><img src="/images/payment-logos/horizontalLogos.png" title="Прием платежей" style="width: 300px;margin-top: 7px;opacity: 0.3;"></div>

        <div class="footer-copyright-logos">
            <div><img src="/images/logo.png"></div>
        </div>
    </div>
    <!--end copyright-->

    {{--
    <div class="footer-background"><img src="/images/bg/3.jpg"></div>
    --}}
    <div class="footer-background"><img src="/images/new/footer.jpg"></div>
</section>