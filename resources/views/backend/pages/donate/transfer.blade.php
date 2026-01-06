@extends('layouts.cabinet')

@section('title', "Отправить " . config('options.coin_name', 'CoL') . " в игру")

@section('wrap')
    <div class="nk-block">
        <div class="row g-gs">
            <div class="col-12">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-title-group">
                            <h5 class="card-title">
                                <span class="mr-2">@yield('title')</span>
                            </h5>
                        </div>
                    </div>
                    <div class="card-inner border-top">
                        <form action="{{ route('donate.transfer') }}" method="POST">
                            @csrf
                            <h2>
                                Доступный баланс: <span class="text-primary">{{ auth()->user()->balance }}</span>
                            </h2>
                            <div class="row g-4">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="char_id">Выбрать персонажа</label>
                                        <div class="form-control-wrap">
                                            <select class="form-select form-control form-control-lg" data-search="on" id="char_id" name="char_id">
                                                @forelse ($characters as $character)
                                                    <option value="{{ $character->char_id }}">{{ $character->account_name }} - {{ $character->char_name }}</option>
                                                @empty
                                                    <option>Нет доступных персонажей</option>
                                                @endforelse
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" for="amount">Введите количество</label>
                                        <div class="form-control-wrap">
                                            <input type="number" class="form-control" id="amount" name="amount"
                                                   value="{{ old('amount') ? old('amount') : auth()->user()->balance }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-lg btn-primary">Перенести</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
