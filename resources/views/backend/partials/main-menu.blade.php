@canany(['admin', 'support', 'investor'])
                <!-- .nk-sidebar-element -->
                <div class="nk-sidebar-element">
                    <div class="nk-sidebar-content">
                        <div class="nk-sidebar-menu" data-simplebar>
                            <ul class="nk-menu">
                                <li class="nk-menu-heading">
                                    <h6 class="overline-title text-primary-alt">{{ __('Администрирование') }}</h6>
                                </li>

                            @can('admin')
                                <!-- .nk-menu-item -->
                                <li class="nk-menu-item has-sub">
                                    <a href="#" class="nk-menu-link nk-menu-toggle" data-original-title="" title="">
                                        <span class="nk-menu-icon"><em class="icon ni ni-users"></em></span>
                                        <span class="nk-menu-text">{{ __('Пользователи') }}</span>
                                        <span class="nk-menu-badge">{{ users_count() }}</span>
                                    </a>
                                    <ul class="nk-menu-sub" style="display: none;">
                                        <li class="nk-menu-item @if (url()->current() == route('users')) nk-menu-item-active @endif">
                                            <a href="{{ route('users') }}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">{{ __('Список пользователей') }}</span></a>
                                        </li>
                                    </ul>
                                    <!-- .nk-menu-sub -->
                                </li>
                                    <!-- .nk-menu-item -->
                                    <li class="nk-menu-item has-sub">
                                        <a href="#" class="nk-menu-link nk-menu-toggle" data-original-title="" title="">
                                            <span class="nk-menu-icon"><em class="icon ni ni-package"></em></span>
                                            <span class="nk-menu-text">{{ __('Кейсы') }}</span>
                                            <span class="nk-menu-badge">{{ сases_count() }}</span>
                                        </a>
                                        <ul class="nk-menu-sub" style="display: none;">
                                            <li class="nk-menu-item @if (url()->current() == route('cases.index')) nk-menu-item-active @endif">
                                                <a href="{{ route('cases.index') }}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">{{ __('Кейсы') }}</span></a>
                                            </li>
                                            <li class="nk-menu-item @if (url()->current() == route('casesitems.index')) nk-menu-item-active @endif">
                                                <a href="{{ route('casesitems.index') }}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">{{ __('Предметы') }}</span></a>
                                            </li>
                                        </ul>
                                        <ul class="nk-menu-sub" style="display: none;">
                                            <li class="nk-menu-item @if (url()->current() == route('bonuses')) nk-menu-item-active @endif">
                                                <a href="{{ route('bonuses') }}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">{{ __('Список выигрышей') }}</span></a>
                                            </li>
                                        </ul>
                                        <ul class="nk-menu-sub" style="display: none;">
                                            <li class="nk-menu-item @if (url()->current() == route('backend.caseopen_history')) nk-menu-item-active @endif">
                                                <a href="{{ route('backend.caseopen_history') }}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">{{ __('Бесплатные открытия') }}</span></a>
                                            </li>
                                        </ul>
                                        <!-- .nk-menu-sub -->
                                    </li>

{{--
                                    <!-- .nk-menu-item -->
                                    <li class="nk-menu-item has-sub">
                                        <a href="#" class="nk-menu-link nk-menu-toggle" data-original-title="" title="">
                                            <span class="nk-menu-icon"><em class="icon ni ni-gift"></em></span>
                                            <span class="nk-menu-text">{{ __('Бонусные подарки') }}</span>
                                        </a>
                                        <ul class="nk-menu-sub" style="display: none;">
                                            <li class="nk-menu-item @if (url()->current() == route('bonuses')) nk-menu-item-active @endif">
                                                <a href="{{ route('bonuses') }}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">{{ __('Список выигрышей') }}</span></a>
                                            </li>
                                        </ul>
                                        <ul class="nk-menu-sub" style="display: none;">
                                            <li class="nk-menu-item @if (url()->current() == route('settings.bonuses')) nk-menu-item-active @endif">
                                                <a href="{{ route('settings.bonuses') }}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">{{ __('Настройки') }}</span></a>
                                            </li>
                                        </ul>
                                        <ul class="nk-menu-sub" style="display: none;">
                                            <li class="nk-menu-item @if (url()->current() == route('settings.bonuses_monday')) nk-menu-item-active @endif">
                                                <a href="{{ route('settings.bonuses_monday') }}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">{{ __('Настройки') }} Monday</span></a>
                                            </li>
                                        </ul>
                                        <ul class="nk-menu-sub" style="display: none;">
                                            <li class="nk-menu-item @if (url()->current() == route('settings.bonuses_thursday')) nk-menu-item-active @endif">
                                                <a href="{{ route('settings.bonuses_thursday') }}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">{{ __('Настройки') }} Thursday</span></a>
                                            </li>
                                        </ul>
                                        <!-- .nk-menu-sub -->
                                    </li>
--}}

                                    <!-- .nk-menu-item -->
                                    <li class="nk-menu-item has-sub">
                                        <a href="#" class="nk-menu-link nk-menu-toggle" data-original-title="" title="">
                                            <span class="nk-menu-icon"><em class="icon ni ni-coins"></em></span>
                                            <span class="nk-menu-text">{{ __('Пожертвования') }}</span>
                                            <span class="nk-menu-badge"></span>
                                        </a>
                                        <ul class="nk-menu-sub" style="display: none;">
                                            <li class="nk-menu-item @if (url()->current() == route('settings.payments')) nk-menu-item-active @endif">
                                                <a href="{{ route('settings.payments') }}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">{{ __('Платежные системы') }}</span></a>
                                            </li>
                                            <li class="nk-menu-item @if (url()->current() == route('settings.donat')) nk-menu-item-active @endif">
                                                <a href="{{ route('settings.donat') }}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">{{ __('Настройки Курса Валют') }}</span></a>
                                            </li>
                                        </ul>
                                        <!-- .nk-menu-sub -->
                                    </li>
                                    <!-- .nk-menu-item -->
                                    <li class="nk-menu-item has-sub">
                                        <a href="#" class="nk-menu-link nk-menu-toggle" data-original-title="" title="">
                                            <span class="nk-menu-icon"><em class="icon ni ni-cart"></em></span>
                                            <span class="nk-menu-text">{{ __('Магазин') }}</span>
                                            <span class="nk-menu-badge">{{ shopitems_count() }}</span>
                                        </a>
                                        <ul class="nk-menu-sub" style="display: none;">
                                            <li class="nk-menu-item @if (url()->current() == route('shopitems.index')) nk-menu-item-active @endif">
                                                <a href="{{ route('shopitems.index') }}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">{{ __('Предметы') }}</span></a>
                                            </li>
                                                <li class="nk-menu-item @if (url()->current() == route('shopsets.index')) nk-menu-item-active @endif">
                                                    <a href="{{ route('shopsets.index') }}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">{{ __('Сеты') }}</span></a>
                                                </li>
                                            <li class="nk-menu-item @if (url()->current() == route('cases.shop_list')) nk-menu-item-active @endif">
                                                <a href="{{ route('cases.shop_list') }}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">{{ __('Кейсы') }}</span></a>
                                            </li>
                                            <li class="nk-menu-item @if (url()->current() == route('shopcategories.index')) nk-menu-item-active @endif">
                                                <a href="{{ route('shopcategories.index') }}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">{{ __('Категории') }}</span></a>
                                            </li>
                                            <li class="nk-menu-item @if (url()->current() == route('settings.services')) nk-menu-item-active @endif">
                                                <a href="{{ route('settings.services') }}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">{{ __('Настройки Услуг') }}</span></a>
                                            </li>
                                        </ul>
                                        <!-- .nk-menu-sub -->
                                    </li>

                                    <!-- .nk-menu-item -->
                                    <li class="nk-menu-item">
                                        <a href="{{ route('promocodes.index') }}" class="nk-menu-link @if (url()->current() == route('promocodes.index')) nk-menu-link-active @endif">
                                            <span class="nk-menu-icon"><em class="icon ni ni-offer"></em></span>
                                            <span class="nk-menu-text">{{ __('Промокоды') }}</span>
                                            <span class="nk-menu-badge">{{ promocodes_count() }}</span>
                                        </a>
                                    </li>
                                @endcan

                                @can('support')
                                    <!-- .nk-menu-item -->
                                    <li class="nk-menu-item">
                                        <a href="{{ route('tickets.all') }}" class="nk-menu-link @if (url()->current() == route('tickets.all')) nk-menu-link-active @endif">
                                            <span class="nk-menu-icon"><em class="icon ni ni-emails"></em></span>
                                            <span class="nk-menu-text">{{ __('Обращения') }}</span>
                                            <span class="nk-menu-badge">{{ tickets_count() }}</span>
                                        </a>
                                    </li>

                                        <!-- .nk-menu-item -->
                                        <li class="nk-menu-item">
                                            <a href="{{ route('backend.delivery_requests') }}" class="nk-menu-link @if (url()->current() == route('backend.delivery_requests')) nk-menu-link-active @endif">
                                                <span class="nk-menu-icon"><em class="icon ni ni-wallet-out"></em></span>
                                                <span class="nk-menu-text">{{ __('Заявки на вывод') }}</span>
                                                <span class="nk-menu-badge">{{ deliveries_count() }}</span>
                                            </a>
                                        </li>
                                @endcan

                                @can('admin')

                                        <!-- .nk-menu-item -->
                                        <li class="nk-menu-item has-sub">
                                            <a href="#" class="nk-menu-link nk-menu-toggle" data-original-title="" title="">
                                                <span class="nk-menu-icon"><em class="icon ni ni-grid-alt"></em></span>
                                                <span class="nk-menu-text">{{ __('Игровые сервера') }}</span>
                                                <span class="nk-menu-badge">{{ servers_count() }}</span>
                                            </a>
                                            <ul class="nk-menu-sub" style="display: none;">
                                                <li class="nk-menu-item @if (url()->current() == route('servers.index')) nk-menu-item-active @endif">
                                                    <a href="{{ route('servers.index') }}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">{{ __('Сервера') }}</span></a>
                                                </li>
                                                <li class="nk-menu-item @if (url()->current() == route('servercategories.index')) nk-menu-item-active @endif">
                                                    <a href="{{ route('servercategories.index') }}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">{{ __('Категории') }}</span></a>
                                                </li>
                                            </ul>
                                            <!-- .nk-menu-sub -->
                                        </li>

                                        <!-- .nk-menu-item -->
                                        <li class="nk-menu-item has-sub">
                                            <a href="#" class="nk-menu-link nk-menu-toggle" data-original-title="" title="">
                                                <span class="nk-menu-icon"><em class="icon ni ni-user"></em></span>
                                                <span class="nk-menu-text">{{ __('Логин и регистрация') }}</span>
                                                <span class="nk-menu-badge"></span>
                                            </a>
                                            <ul class="nk-menu-sub" style="display: none;">
                                                <li class="nk-menu-item @if (url()->current() == route('settings.login_steam')) nk-menu-item-active @endif">
                                                    <a href="{{ route('settings.login_steam') }}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">{{ __('Настройки') }} Steam</span></a>
                                                </li>
                                            </ul>
                                            <!-- .nk-menu-sub -->
                                        </li>


                                            <!-- .nk-menu-item -->
                                            <li class="nk-menu-item has-sub">
                                                <a href="#" class="nk-menu-link nk-menu-toggle" data-original-title="" title="">
                                                    <span class="nk-menu-icon"><em class="icon ni ni-link"></em></span>
                                                    <span class="nk-menu-text">{{ __('Настройки') }} APIs</span>
                                                </a>
                                                <ul class="nk-menu-sub" style="display: none;">
                                                    <li class="nk-menu-item @if (url()->current() == route('settings.waxpeer_api')) nk-menu-item-active @endif">
                                                        <a href="{{ route('settings.waxpeer_api') }}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">{{ __('Waxpeer API') }}</span></a>
                                                    </li>
                                                    <li class="nk-menu-item @if (url()->current() == route('settings.skinsback_api')) nk-menu-item-active @endif">
                                                        <a href="{{ route('settings.skinsback_api') }}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">{{ __('Skinsback API') }}</span></a>
                                                    </li>
                                                    <li class="nk-menu-item @if (url()->current() == route('settings.game_api')) nk-menu-item-active @endif">
                                                        <a href="{{ route('settings.game_api') }}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">{{ __('Game RestAPI') }}</span></a>
                                                    </li>
                                                </ul>
                                                <!-- .nk-menu-sub -->
                                            </li>
                                @endcan

                                @can('admin')
                                    <li class="nk-menu-heading">
                                        <h6 class="overline-title text-primary-alt">{{ __('Контент сайта') }}</h6>
                                    </li>
                                    <!-- .nk-menu-item -->
                                    <li class="nk-menu-item has-sub">
                                        <a href="#" class="nk-menu-link nk-menu-toggle" data-original-title="" title="">
                                            <span class="nk-menu-icon"><em class="icon ni ni-setting"></em></span>
                                            <span class="nk-menu-text">{{ __('Настройки сайта') }}</span>
                                        </a>
                                        <ul class="nk-menu-sub" style="display: none;">
                                            <li class="nk-menu-item @if (url()->current() == route('settings.project_name')) nk-menu-item-active @endif">
                                                <a href="{{ route('settings.project_name') }}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">{{ __('Название проекта') }}</span></a>
                                            </li>
                                            <li class="nk-menu-item @if (url()->current() == route('settings.robots')) nk-menu-item-active @endif">
                                                <a href="{{ route('settings.robots') }}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">{{ __('Robots.txt') }}</span></a>
                                            </li>
                                            <li class="nk-menu-item @if (url()->current() == route('settings.sitemap')) nk-menu-item-active @endif">
                                                <a href="{{ route('settings.sitemap') }}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">{{ __('Sitemap') }}</span></a>
                                            </li>
                                            <li class="nk-menu-item @if (url()->current() == route('settings.langs')) nk-menu-item-active @endif">
                                                <a href="{{ route('settings.langs') }}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">{{ __('Языки') }}</span></a>
                                            </li>
                                            <li class="nk-menu-item @if (url()->current() == route('settings.analitics')) nk-menu-item-active @endif">
                                                <a href="{{ route('settings.analitics') }}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">{{ __('Счетчики и аналитика') }}</span></a>
                                            </li>
                                            <li class="nk-menu-item @if (url()->current() == route('settings.recaptcha')) nk-menu-item-active @endif">
                                                <a href="{{ route('settings.recaptcha') }}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">{{ __('Recaptcha') }}</span></a>
                                            </li>
                                            <li class="nk-menu-item @if (url()->current() == route('settings.smtp')) nk-menu-item-active @endif">
                                                <a href="{{ route('settings.smtp') }}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">{{ __('SMTP') }}</span></a>
                                            </li>
                                            <li class="nk-menu-item @if (url()->current() == route('settings.falling_snow')) nk-menu-item-active @endif">
                                                <a href="{{ route('settings.falling_snow') }}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">{{ __('Падающий снег') }}</span></a>
                                            </li>
                                        </ul>
                                        <!-- .nk-menu-sub -->
                                    </li>

                                    <!-- .nk-menu-item -->
                                    <li class="nk-menu-item">
                                        <a href="{{ route('settings.site') }}" class="nk-menu-link @if (url()->current() == route('settings.site')) nk-menu-link-active @endif">
                                            <span class="nk-menu-icon"><em class="icon ni ni-home"></em></span>
                                            <span class="nk-menu-text">{{ __('Главная') }}</span>
                                        </a>
                                    </li>
                                    <!-- .nk-menu-item -->
                                    <li class="nk-menu-item">
                                        <a href="{{ route('banners.index') }}" class="nk-menu-link @if (url()->current() == route('banners.index')) nk-menu-link-active @endif">
                                            <span class="nk-menu-icon"><em class="icon ni ni-img-fill"></em></span>
                                            <span class="nk-menu-text">{{ __('Баннеры') }}</span>
                                            <span class="nk-menu-badge">{{ banners_count() }}</span>
                                        </a>
                                    </li>
                                    <!-- .nk-menu-item -->
                                    <li class="nk-menu-item">
                                        <a href="{{ route('articles.index') }}" class="nk-menu-link @if (url()->current() == route('articles.index')) nk-menu-link-active @endif">
                                            <span class="nk-menu-icon"><em class="icon ni ni-article"></em></span>
                                            <span class="nk-menu-text">{{ __('Новости') }}</span>
                                            <span class="nk-menu-badge">{{ articles_count() }}</span>
                                        </a>
                                    </li>
                                    <!-- .nk-menu-item -->
                                    <li class="nk-menu-item has-sub">
                                        <a href="#" class="nk-menu-link nk-menu-toggle" data-original-title="" title="">
                                            <span class="nk-menu-icon"><em class="icon ni ni-book"></em></span>
                                            <span class="nk-menu-text">{{ __('Гайды') }}</span>
                                            <span class="nk-menu-badge">{{ guides_count() }}</span>
                                        </a>
                                        <ul class="nk-menu-sub" style="display: none;">
                                            <li class="nk-menu-item @if (url()->current() == route('guides.index')) nk-menu-item-active @endif">
                                                <a href="{{ route('guides.index') }}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">{{ __('Гайды') }}</span></a>
                                            </li>
                                            <li class="nk-menu-item @if (url()->current() == route('guidecategories.index')) nk-menu-item-active @endif">
                                                <a href="{{ route('guidecategories.index') }}" class="nk-menu-link" data-original-title="" title=""><span class="nk-menu-text">{{ __('Категории') }}</span></a>
                                            </li>
                                        </ul>
                                        <!-- .nk-menu-sub -->
                                    </li>
                                    <!-- .nk-menu-item -->
                                    <li class="nk-menu-item">
                                        <a href="{{ route('faqs.index') }}" class="nk-menu-link @if (url()->current() == route('faqs.index')) nk-menu-link-active @endif">
                                            <span class="nk-menu-icon"><em class="icon ni ni-chat-circle"></em></span>
                                            <span class="nk-menu-text">{{ __('Частые вопросы') }}</span>
                                            <span class="nk-menu-badge">{{ faqs_count() }}</span>
                                        </a>
                                    </li>
                                    <!-- .nk-menu-item -->
                                    <li class="nk-menu-item">
                                        <a href="{{ route('settings.social') }}" class="nk-menu-link @if (url()->current() == route('settings.social')) nk-menu-link-active @endif">
                                            <span class="nk-menu-icon"><em class="icon ni ni-signal"></em></span>
                                            <span class="nk-menu-text">{{ __('Соц сети') }}</span>
                                        </a>
                                    </li>
                                    <!-- .nk-menu-item -->
                                    <li class="nk-menu-item">
                                        <a href="{{ route('settings.forum') }}" class="nk-menu-link @if (url()->current() == route('settings.forum')) nk-menu-link-active @endif">
                                            <span class="nk-menu-icon"><em class="icon ni ni-rss"></em></span>
                                            <span class="nk-menu-text">{{ __('Форум') }}</span>
                                        </a>
                                    </li>
                                    <!-- .nk-menu-item -->
                                    <li class="nk-menu-item">
                                        <a href="{{ route('settings.policy') }}" class="nk-menu-link @if (url()->current() == route('settings.policy')) nk-menu-link-active @endif">
                                            <span class="nk-menu-icon"><em class="icon ni ni-reports-alt"></em></span>
                                            <span class="nk-menu-text">{{ __('Соглашение и политика') }}</span>
                                        </a>
                                    </li>

                                    @endcan

                                @can('admin')
                                <!-- .nk-menu-item -->
                                <li class="nk-menu-heading">
                                    <h6 class="overline-title text-primary-alt">{{ __('Статус серверов') }}</h6>
                                </li>
                                @foreach(getservers() as $server)
                                <!-- .nk-menu-item -->
                                <li class="nk-menu-item">
                                    <a href="{{ route('server.settings', $server) }}" class="nk-menu-link">
                                        <span class="nk-menu-icon"><em class="icon ni ni-server"></em></span>
                                        <span class="nk-menu-text">{{ $server->name }}</span>
                                        <span class="badge badge-pill badge-{{ server_status($server->id) === 'Online' ? 'success' : 'danger' }}">
                                            {{ server_status($server->id) }} @if(server_status($server->id) === 'Online') ({{ online_count($server->id) }}) @endif
                                        </span>

                                    </a>
                                </li>
                                @endforeach

                                @endcan

                                @can('investor')
                                <li class="nk-menu-heading">
                                    <h6 class="overline-title text-primary-alt">{{ __('Логи и статистика') }}</h6>
                                </li>
                                <!-- .nk-menu-item -->
                                <li class="nk-menu-item @if (url()->current() == route('logs.payments')) nk-menu-item-active @endif">
                                    <a href="{{ route('logs.payments') }}" class="nk-menu-link">
                                        <span class="nk-menu-icon"><em class="icon ni ni-wallet-saving"></em></span>
                                        <span class="nk-menu-text">{{ __('Статистика платежей') }}</span>
                                    </a>
                                </li>
                                    <!-- .nk-menu-item -->
                                    <li class="nk-menu-item @if (url()->current() == route('logs.shop')) nk-menu-item-active @endif">
                                        <a href="{{ route('logs.shop') }}" class="nk-menu-link">
                                            <span class="nk-menu-icon"><em class="icon ni ni-cart"></em></span>
                                            <span class="nk-menu-text">{{ __('Статистика магазина') }}</span>
                                        </a>
                                    </li>
                                <!-- .nk-menu-item -->
                                <li class="nk-menu-item @if (url()->current() == route('logs.registrations')) nk-menu-item-active @endif">
                                    <a href="{{ route('logs.registrations') }}" class="nk-menu-link">
                                        <span class="nk-menu-icon"><em class="icon ni ni-user"></em></span>
                                        <span class="nk-menu-text">{{ __('Статистика регистрации') }}</span>
                                    </a>
                                </li>
                                <!-- .nk-menu-item -->
                                    <li class="nk-menu-item @if (url()->current() == route('logs.visits')) nk-menu-item-active @endif">
                                        <a href="{{ route('logs.visits') }}" class="nk-menu-link">
                                            <span class="nk-menu-icon"><em class="icon ni ni-activity-alt"></em></span>
                                            <span class="nk-menu-text">{{ __('Статистика посещений') }}</span>
                                        </a>
                                    </li>
                                    <!-- .nk-menu-item -->
                                <li class="nk-menu-item @if (url()->current() == route('logs.gamecurrencylogs')) nk-menu-item-active @endif">
                                    <a href="{{ route('logs.gamecurrencylogs') }}" class="nk-menu-link">
                                        <span class="nk-menu-icon"><em class="icon ni ni-invest"></em></span>
                                        <span class="nk-menu-text">{{ __('Логи игровой валюты') }}</span>
                                    </a>
                                </li>
                                <!-- .nk-menu-item -->
                                <li class="nk-menu-item @if (url()->current() == route('logs.adminlogs')) nk-menu-item-active @endif">
                                    <a href="{{ route('logs.adminlogs') }}" class="nk-menu-link">
                                        <span class="nk-menu-icon"><em class="icon ni ni-file-docs"></em></span>
                                        <span class="nk-menu-text">{{ __('Логи администраторов') }}</span>
                                    </a>
                                </li>
                                <!-- .nk-menu-item -->
                                <li class="nk-menu-item @if (url()->current() == route('logs.adminlogs')) nk-menu-item-active @endif">
                                    <a href="{{ route('logs.servererrors') }}" class="nk-menu-link">
                                        <span class="nk-menu-icon"><em class="icon ni ni-alert-circle"></em></span>
                                        <span class="nk-menu-text">{{ __('Ошибки сервера') }}</span>
                                    </a>
                                </li>
                                @endcan

                                    @can('admin')
                                    <li class="nk-menu-heading">
                                        <h6 class="overline-title text-primary-alt">{{ __('Аккаунт') }}</h6>
                                    </li>
                                    <!-- .nk-menu-item -->
                                    <li class="nk-menu-item @if (url()->current() == route('backend.settings.profile')) nk-menu-item-active @endif">
                                        <a href="{{ route('backend.settings.profile') }}" class="nk-menu-link">
                                            <span class="nk-menu-icon"><em class="icon ni ni-setting-alt"></em></span>
                                            <span class="nk-menu-text">{{ __('Настройки профиля') }}</span>
                                        </a>
                                    </li>
                                    <!-- .nk-menu-item -->
                                    <li class="nk-menu-item @if (url()->current() == route('backend.settings.activity')) nk-menu-item-active @endif">
                                        <a href="{{ route('backend.settings.activity') }}" class="nk-menu-link">
                                            <span class="nk-menu-icon"><em class="icon ni ni-activity-alt"></em></span>
                                            <span class="nk-menu-text">{{ __('Активные устройства') }}</span>
                                        </a>
                                    </li>
                                    <!-- .nk-menu-item -->
                                    <li class="nk-menu-item @if (url()->current() == route('backend.settings.security')) nk-menu-item-active @endif" style="display:none;">
                                        <a href="{{ route('backend.settings.security') }}" class="nk-menu-link">
                                            <span class="nk-menu-icon"><em class="icon ni ni-lock-alt-fill"></em></span>
                                            <span class="nk-menu-text">{{ __('Настройки безопасности') }}</span>
                                        </a>
                                    </li>

                                @endcan

                            </ul><!-- .nk-menu -->
                        </div><!-- .nk-sidebar-menu -->
                    </div><!-- .nk-sidebar-content -->
                </div><!-- .nk-sidebar-element -->

@endcan