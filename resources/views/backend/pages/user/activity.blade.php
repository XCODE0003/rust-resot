@extends('backend.layouts.backend')

@section('title', __('Панель управления') . ' - ' . __('Активные устройства'))
@section('headerTitle', __('Аккаунт'))
@section('headerDesc', __('Активные устройства') . '.')

@section('wrap')
    <!-- .nk-block -->
    <div class="nk-block">
        <div class="row g-gs">
            <div class="col-12">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-head flex-column align-items-start">
                            <div class="nk-block-des">
                                <p>{{ __('При необходимости вы можете выйти из всех других сеансов браузера на всех ваших устройствах. Некоторые из ваших недавних сеансов перечислены ниже; однако этот список может быть не исчерпывающим. Если вы считаете, что ваша учетная запись была скомпрометирована, вам также следует обновить пароль.') }}</p>
                            </div>
                        </div>
                        <table class="table table-ulogs">
                            <thead class="thead-light">
                            <tr>
                                <th class="tb-col-os"><span class="overline-title">{{ __('Браузер') }} <span class="d-sm-none">/ IP</span></span></th>
                                <th class="tb-col-ip"><span class="overline-title">IP</span></th>
                                <th class="tb-col-time"><span class="overline-title">{{ __('Последняя активность') }}</span></th>
                                <th class="tb-col-action"><span class="overline-title">&nbsp;</span></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($sessions as $session)
                                <tr>
                                    <td class="tb-col-os">{{ $session->getBrowser() }} на {{ $session->getOs() }}</td>
                                    <td class="tb-col-ip"><span class="sub-text">{{ $session->ip_address }}</span></td>
                                    <td class="tb-col-time"><span class="sub-text">{{ date('d.m.Y', $session->last_activity) }} <span class="d-none d-sm-inline-block">{{ date('H:i', $session->last_activity) }}</span></span></td>
                                    <td class="tb-col-action">
                                        @if (session()->getId() !== $session->id)
                                            <a href="{{ route('backend.settings.activity.destroy', $session->id) }}" title="{{ __('Выйти с устройства') }}">
                                                <span class="badge badge-outline-danger">{{ __('Отключить') }}</span>
                                            </a>
                                        @else
                                            <span class="badge badge-outline-success">{{ __('Данное устройство') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- .nk-block -->
@endsection
