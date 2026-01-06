@extends('backend.layouts.backend')

@section('title', __('Панель управления') . ' - ' . __('Настройки профиля'))
@section('headerTitle', __('Аккаунт'))
@section('headerDesc', __('Настройки профиля') . '.')

@section('wrap')
    <!-- .nk-block -->
    <div class="nk-block">
        <div class="row g-gs">
            <div class="col-12">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-head">
                        </div>
                        <form method="POST">
                            @csrf
                            @method('PATCH')

                            <div class="row g-4">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="name">{{ __('Имя') }}</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                   id="name" name="name" value="{{ auth()->user()->name }}" required readonly>
                                            @error('name')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="email">E-Mail</label>
                                        <div class="form-control-wrap">
                                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                                   id="email" name="email" value="{{ auth()->user()->email }}" required readonly>
                                            @error('email')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-lg btn-primary ml-auto">{{ __('Изменить') }}</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>


            <div class="col-12">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-head">
                        </div>
                        <form method="POST">
                            @csrf
                            @method('PATCH')

                            <div class="row g-4">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="password">{{ __('Текущий пароль') }}</label>
                                        <div class="form-control-wrap">
                                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                                   id="password" name="password" required readonly>
                                            @error('password')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="new_password">{{ __('Новый пароль') }}</label>
                                        <div class="form-control-wrap">
                                            <input type="password" class="form-control @error('new_password') is-invalid @enderror"
                                                   id="new_password" name="new_password" required readonly>
                                            @error('new_password')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="new_password_confirmation">{{ __('Повторите новый пароль') }}</label>
                                        <div class="form-control-wrap">
                                            <input type="password" class="form-control @error('new_password_confirmation') is-invalid @enderror"
                                                   id="new_password_confirmation" name="new_password_confirmation" required readonly>
                                            @error('new_password_confirmation')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-lg btn-primary ml-auto">{{ __('Изменить') }}</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>


        </div>
    </div>
    <!-- .nk-block -->
@endsection
