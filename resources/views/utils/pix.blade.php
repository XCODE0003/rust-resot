@extends('layouts.main')

@section('title', __('Пожертвование'))

@section('main')
    <main>

        <link rel="stylesheet" href="{{ asset('assets/css/ion.rangeSlider.css') }}"/>
        <link rel="stylesheet" href="{{ asset('css/css-update.css') }}"/>


        <style>
            .anim {
                display: none;
            }
        </style>

        <div class="container donation-container">
            <div class="main__container _container inner-content" style="background-color: white;padding: 100px;text-align: center;">
		<h3>Scan BRCode to proceed with payment</h3>

                {{-- Alert --}}
                @foreach (['danger', 'warning', 'success', 'info'] as $type)
                    @if(Session::has('alert.' . $type))
                        @foreach(Session::get('alert.' . $type) as $message)
                            <div class="alert alert-fill alert-{{ $type }} alert-dismissible alert-icon">
                                @if ($type === 'danger')
                                    <em class="icon ni ni-cross-circle"></em>
                                @elseif($type === 'success')
                                    <em class="icon ni ni-check-circle"></em>
                                @else
                                    <em class="icon ni ni-alert-circle"></em>
                                @endif
                                {{ $message }}
                                <button class="close" data-dismiss="alert"></button>
                            </div>
                        @endforeach
                    @endif
                @endforeach
                @php
                    session()->forget(['alert.danger', 'alert.warning', 'alert.success', 'alert.info']);
                @endphp
                {{-- End Alert --}}

		<img src="data:image/png;base64, {!! $qrcode !!}" alt="BRcode payment" width="159" height="159"/>

            </div>
        </div>

    </main>
@endsection