@extends('layouts.dashlite')
@section('body')

	<!-- Google Tag Manager (noscript) -->
	<noscript>
		<iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TXKW6WM"
					  height="0" width="0" style="display:none;visibility:hidden"></iframe>
	</noscript>
	<!-- End Google Tag Manager (noscript) -->

	@if(url()->current() == 'https://rustresort.com/store/8')
		<style>
			.inner {
				background: none;
			}
		</style>
	@endif

	<!--loading-->
	@if(!session()->has('first_visit'))
		@include('partials.main.loading')
		@php session()->put('first_visit', '86400'); @endphp
	@endif
	<!--end loading-->

	<!--background header-->
	@include('partials.main.background')
	<!--background header-->

	<!--header-->
	@include('partials.main.header')
	<!--end header-->

	@yield('content')

	<!--footer-->
	@include('partials.main.footer')
	<!--end footer-->

	<!-- end wrapper -->

	<!--login up modal-->
	@include('partials.main.modal')
	<!--end login up modal-->

	@include('partials.main.bonus_cases')


	@if(isset(auth()->user()->role) && auth()->user()->role == 'admin' && 1==2)
		@if(config('options.bonus_status', '0') == '1')
			@include('partials.main.bonus')
		@endif
		@if(config('options.bonusm_status', '0') == '1')
			@include('partials.main.bonus_monday')
		@endif
		@if(config('options.bonusth_status', '0') == '1')
			@include('partials.main.bonus_thursday')
		@endif
	@endif

@endsection