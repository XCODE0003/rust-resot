<li class="dropdown user-dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
        <div class="user-toggle">
            <div class="user-avatar sm">
                <em class="icon ni ni-user-alt"></em>
            </div>
            <div class="user-info d-none d-md-block">
                @if(auth()->user()->role == 'investor')
                    <div class="user-status">{{ __('Аналитик') }}</div>
                @elseif(auth()->user()->role == 'support')
                    <div class="user-status">{{ __('Агент Поддержки') }}</div>
                @else
                    <div class="user-status">{{ __('Администратор') }}</div>
                @endif
                <div class="user-name dropdown-indicator">{{ auth()->user()->name }}</div>
            </div>
        </div>
    </a>
    <div class="dropdown-menu dropdown-menu-md dropdown-menu-right dropdown-menu-s1">
        <div class="dropdown-inner user-card-wrap bg-lighter d-none d-md-block">
            <div class="user-card">
                <div class="user-avatar">
                    <em class="icon ni ni-user-alt"></em>
                </div>
                <div class="user-info">
                    <span class="lead-text">{{ auth()->user()->name }}</span>
                    <span class="sub-text">{{ auth()->user()->email }}</span>
                </div>
            </div>
        </div>

        @can('admin')
            <div class="dropdown-inner">
                <ul class="link-list">
                    <li><a href="{{ route('backend.settings.profile') }}"><em class="icon ni ni-setting-alt"></em><span>{{ __('Настройки профиля') }}</span></a></li>
                    <li><a href="{{ route('backend.settings.activity') }}"><em class="icon ni ni-activity-alt"></em><span>{{ __('Активные устройства') }}</span></a></li>
                </ul>
            </div>
        @endcan

        <div class="dropdown-inner">
            <ul class="link-list">
                <li><a href="{{ route('logout') }}"><em class="icon ni ni-signout"></em><span>{{ __('Выход') }}</span></a></li>
            </ul>
        </div>
    </div>
</li>
