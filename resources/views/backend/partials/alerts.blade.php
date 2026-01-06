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
{{-- End Alert --}}