@extends('base.base')

@section('pageTitle', 'Dashboard')

@section('content')
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="toolbar" id="kt_toolbar">
        <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
            <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">Dashboard</h1>
            </div>

        </div>
    </div>
    <div class="app-content  flex-column-fluid " id="kt_post">
        <div class="" id="kt_post">
            <div id="kt_content_container" class="container-xxl">
                <div class="card mb-5 mb-xl-8">
                    <div class="card-header border-0 pt-5">

                        <h3>Accounts balance</h3>
                        <div class="col-md-12">
                            <!-- Селект с именем exchange_settings и классом js-exchange-select -->
                            <select name="exchange_settings" class="js-exchange-select form-control form-select form-select-solid">
                                @foreach(\App\Models\ExchangeSetting::all() as $setting)
                                    <option value="{{ $setting->id }}">{{ $setting->name }}</option>
                                @endforeach
                            </select>

                            <!-- Таблица для отображения данных -->
                            <table id="balanceTable" class="table table-row-bordered table-row-gray-100 align-middle gs-0 gy-3">
                                <thead>
                                <tr>
                                    <th>Asset</th>
                                    <th>Free</th>
                                    <th>Locked</th>
                                </tr>
                                </thead>
                                <tbody>
                                <!-- Данные будут отображаться здесь -->
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
                        <div class="app-content  flex-column-fluid " id="kt_post">
                            <div class="" id="kt_post">
                                <div id="kt_content_container" class="container-xxl">
                                    <div class="card mb-5 mb-xl-8">
                                        <div class="card-header border-0 pt-5">

                        <div class="col-md-12">
                        <h3>Make order</h3>
                        <form id="formOrder" action="{{ route('makeorder') }}">
                        <div class="col">
                            <select name="CurrencyPair" class="form-control form-select form-select-solid" data-kt-select2="true" data-kt-data-dropdown-parent="#kt_menu_61484bf44f851">
                                @foreach(\App\Models\CurrencyPair::all() as $CurrencyPair)
                                    <option value="{{ $CurrencyPair->id }}">{{ $CurrencyPair->sell_currency }} / {{ $CurrencyPair->buy_currency }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <input name="quantity" class="form-control form-select form-select-solid" placeholder="Quantity">
                        </div>
                            <div class="col">
                                <select name="type" class="form-control form-select form-select-solid" data-kt-select2="true" data-kt-data-dropdown-parent="#kt_menu_61484bf44f851">
                                    <option value="BUY">BUY</option>
                                    <option value="SELL">SELL</option>
                                </select>
                            </div>
                        <div class="col">
                            <select name="ordertype" class="form-control form-select form-select-solid" data-kt-select2="true" data-kt-data-dropdown-parent="#kt_menu_61484bf44f851">
                                <option value="MARKET">MARKET</option>
                                <option value="LIMIT">LIMIT</option>
                            </select>
                        </div>
                        <div class="col">
                            <input name="price" class="form-control form-select form-select-solid" placeholder="price">
                        </div>
                        <div class="col">
                            <button type="submit" id="kt_account_profile_details_submit" class="btn btn-primary">
                                <span class="indicator-label"> Make order </span></span>
                            </button>
                        </div>
                        <div class="col" id="rezultOrder">

                        </div>
                        </form>
                            <br><br><br><br>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>


    <div class="app-content  flex-column-fluid " id="kt_post">
        <div class="" id="kt_post">
            <div id="kt_content_container" class="container-xxl">
                <div class="card mb-5 mb-xl-8">
                    <div class="card-header border-0 pt-5">

                        <h3>Bot calculator</h3>
                        <div class="col-md-12">
                            <!-- Селект с именем exchange_settings и классом js-exchange-select -->
                            <select name="exchange_bot" class="js-bot-select form-control form-select form-select-solid">
                                @foreach(\App\Models\Bot::all() as $setting)
                                    <option value="{{ $setting->id }}">{{ $setting->name }}</option>
                                @endforeach
                            </select>

                            <!-- Таблица для отображения данных -->
                            <pre id="botRez">

                            </pre>
<br><br><br>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end::Post-->
</div>


@endsection

@section('pageScript')
    $(document).ready(function () {
    $('.js-bot-select').on('change', function () {
    var selectedId = $(this).val();

    // Отправка GET запроса на сервер
    $.get('/get-price?id=' + selectedId, function (data) {
    // Очищаем таблицу перед добавлением новых данных
    $('#botRez').html(data);

    });
    });
    $('.js-bot-select').trigger('change');
    });

    $(document).ready(function () {
    $('.js-exchange-select').on('change', function () {
    var selectedId = $(this).val();

    // Отправка GET запроса на сервер
    $.get('/get-balancejson/' + selectedId, function (data) {
    // Очищаем таблицу перед добавлением новых данных
    $('#balanceTable tbody').empty();

    // Добавляем данные в таблицу
    $.each(data, function (index, item) {
    $('#balanceTable tbody').append('<tr>' +
        '<td>' + item.asset + '</td>' +
        '<td>' + item.free + '</td>' +
        '<td>' + item.locked + '</td>' +
        '</tr>');
    });
    });
    });
    $('.js-exchange-select').trigger('change');
    });

    $(document).ready(function () {
    $('#formOrder').submit(function (e) {
    e.preventDefault();

    var form = $(this);
    var url = form.attr('action');

    $.ajax({
    type: "POST",
    url: url,
    data: form.serialize(),
    success: function (data) {
    // Выводим результаты ордера в элемент с id="rezultOrder"
    $('#rezultOrder').JSONView(data);
    }
    });
    });
    });
@endsection