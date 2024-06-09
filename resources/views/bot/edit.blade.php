@extends('base.base')

@section('pageTitle', $settings['title'])

@section('content')



        <div class="post d-flex flex-column-fluid" id="kt_post">

            <div id="kt_content_container" class="container-xxl">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="card mb-5 mb-xl-8">
                    <div class="card-header border-0"><div class="card-title m-0"><h3 class="fw-bolder m-0">{{__('plans.edit')}}</h3></div></div>
                    {{ Form::open(['route' => ['bot.update',$model->id]]) }}

                    @method('PUT')
                    <div class="card-body py-3">

                        @foreach ($settings['form'] as $fname => $field)
                        <div class="row mb-6 pt-2">
                            <label class="col-lg-4 col-form-label fw-bold fs-6">
                                <span>{{ $settings['attr'][$fname] }}</span>
                            </label>
                            <div class="col-lg-8 fv-row">
                                @if($field=='textarea')
                                    {{ Form::textarea($fname, $model->$fname, ['placeholder'=> $settings['attr'][$fname], 'class'=>'form-control form-control-lg form-control-solid', 'required'=>'required']) }}
                                @endif
                                @if($field=='text')
                                        {{ Form::text($fname, $model->$fname, ['placeholder'=> $settings['attr'][$fname], 'class'=>'form-control form-control-lg form-control-solid', 'required'=>'required']) }}
                                @endif
                                    @if($field=='time')
                                        {{ Form::input('time',$fname, $model->$fname, ['placeholder'=> $settings['attr'][$fname], 'class'=>'form-control form-control-lg form-control-solid', 'required'=>'required']) }}
                                    @endif
                                    @if($field=='password')
                                        {{ Form::password($fname, ['placeholder'=> $settings['attr'][$fname], 'class'=>'form-control form-control-lg form-control-solid', 'required'=>'required']) }}
                                            </div>
                        </div><div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-bold fs-6">
                                <span>{{__('bot.rpwd')}}</span>
                            </label>
                                            <div class="col-lg-8 fv-row">
                                            <input type="password" class="form-control form-control-lg form-control-solid" name="password_confirmation">
                                    @endif
                                        @if(is_array($field))
                                                    @php
                                                        $pairsArr = json_decode($model->$fname);


                                                        @endphp

                                                    @if($fname=='currency_pairs')

                                                        @php

                                                    if($pairsArr=='1'){
//dd($pairsArr);
                                                    $pairsArr=['BTC'=>'ETH'];
                                                    }

                                                        $i=0;

                                                        $lastVal = '';

                                                        foreach ($pairsArr as  $k => $p){

                                                        $key = trim(explode(" → ",$p)[0]);
                                                        $value = trim(explode(" → ",$p)[1]);

if(!empty($key)){
                                                            if(empty($lastVal)){
                                                            if(@\App\Models\CurrencyPair::selectRaw("*")
                                                            ->where('status', 'TRADING')
                                                            ->where('buy_currency', $key)
                                                            ->where('sell_currency', $value)
                                                            ->first()->id){
                                                            //echo $key.'-'.$value.'|';
                                                        echo Form::select($fname, $field[0], \App\Models\CurrencyPair::selectRaw("*")
                                                            ->where('status', 'TRADING')
                                                            ->where('buy_currency', $key)
                                                            ->where('sell_currency', $value)
                                                            ->first()->id,$field[1]);
                                                            $lastVal = $value;
                                                            }else {
                                                            break;
                                                            }
                                                            }else {
                                                        echo Form::select($fname, \App\Models\CurrencyPair::selectRaw("
    CASE
        WHEN buy_currency = '{$lastVal}' THEN CONCAT(buy_currency, ' → ', sell_currency)
        WHEN sell_currency = '{$lastVal}' THEN CONCAT(sell_currency, ' → ', buy_currency)
        ELSE CONCAT(buy_currency, ' → ', sell_currency)
    END as name,
    id
")
            ->where('status', 'TRADING')
            ->whereRaw("(buy_currency='".$lastVal."' or sell_currency='".$lastVal."') ")
            ->get()
            ->pluck('name', 'id')
            ->toArray(), \App\Models\CurrencyPair::selectRaw("*")
                                                            ->where('status', 'TRADING')
                                                            ->whereRaw("(buy_currency='".$lastVal."' and sell_currency='".$value."') OR (sell_currency='".$lastVal."' and buy_currency='".$value."')")
                                                             ->first()->id,['class' => 'form-control form-select form-select-solid listAddS OnlyNew ggg','data-kt-select2' => 'true','onchange' => 'ReCalcSelect($(this));']);
                                                            $lastVal = $value;
                                                            }
}else {
echo Form::select($fname, $field[0], $model->$fname,$field[1]);
}
                                                        }

                                                        @endphp



                                                        <div id="dynamicSelects">
                                                            <!-- Здесь будут динамически добавленные селекты -->
                                                        </div>
                                                        @else
                                                        {{ Form::select($fname, $field[0], $model->$fname,$field[1]) }}
                                                    @endif

                                        @endif
                            </div>
                        </div>
                        @endforeach

                            <input name="pairsJson" id="pairsJson" type="hidden">
                    </div>
                    <div class="card-footer d-flex justify-content-end py-6 px-9">
                        <a href="{{ route('bot.index') }}" class="btn btn-white btn-active-light-primary me-2"> {{__('bot.cansel')}} </a>
                        <button type="submit" id="kt_account_profile_details_submit" class="btn btn-primary">
                            <span class="indicator-label"> {{__('bot.edit')}} </span></span>
                        </button>
                    </div>
                    {{ Form::close() }}
                </div>

            </div>
        </div>


@endsection


@section('pageScript')

    function MakeJson() {
    var selectedOptions = [];

    // Проходим по всем селектам с классом "listAddS"
    $(".listAddS").each(function () {
    var selectedOption = $(this).find("option:selected");
    if (selectedOption.length > 0) {
    selectedOptions.push(selectedOption.text());
    }
    });

    // Формируем JSON из выбранных вариантов
    var pairsJson = JSON.stringify(selectedOptions);

    // Помещаем JSON в поле с классом "pairsJson"
    $('#pairsJson').val(pairsJson);
    }

    function ReCalcSelect(selected) {
    // Находим все элементы с классом .OnlyNew, которые идут после выбранного
    var elementsToRemove = selected.nextAll(".OnlyNew");
    // Удаляем найденные элементы
    //elementsToRemove.html();
    elementsToRemove.nextAll("span").remove();
    elementsToRemove.remove();

    console.log(elementsToRemove.length);
    var selectedId = selected.val(); // Получаем выбранное значение
    var thisSelected = selected.find("option:selected").text();
    var thisparts = thisSelected.split("→");
    console.log(thisSelected);
    // Находим первый и последний select с классом "listAddS"
    var firstSelect = $(".listAddS:first");
    var lastSelect = $(".listAddS:last");

    // Получаем текст выбранного option в первом селекте
    var firstselectedOptionText = firstSelect.find("option:selected").text();
    // Разделяем текст по символу "→"
    var firstparts = firstselectedOptionText.split("→");

    // Получаем текст выбранного option в первом селекте
    var lastselectedOptionText = selected.find("option:selected").text();
    // Разделяем текст по символу "→"
    var lastparts = lastselectedOptionText.split("→");
    console.log("if("+firstparts.length+" === 2 && "+lastparts.length+" === 2 && "+firstparts[0]+" === "+lastparts[1]+" && "+$(".listAddS").length+">1)");
    if(firstparts.length === 2 && lastparts.length === 2 && firstparts[0].trim() === lastparts[1].trim() && $(".listAddS").length>1){
    selectedId=false;
    $('#kt_account_profile_details_submit').removeAttr('disabled');
    }else {
    $('#kt_account_profile_details_submit').attr('disabled','disabled');

    }

    if (selectedId) {
    // Выполняем AJAX-запрос для загрузки данных на основе выбранного id
    $.ajax({
    url: '{{ route('loaddata') }}?id=' + thisparts[1].trim(), // Замените на URL вашего обработчика
    method: 'GET',
    success: function (data) {
    //$('.newAdded'+selectedId).remove();
    // Создаем новый селект и добавляем его в #dynamicSelects
    var newSelect = $('<select data-kt-select2="true" class="form-control form-select form-select-solid listAddS OnlyNew newAdded'+selectedId+'" onchange="ReCalcSelect($(this));">');
        newSelect.attr("name", "dynamicSelect[]");
        newSelect.append("<option value=''>Выберите опцию</option>");
        $.each(data, function (key, value) {
        newSelect.append("<option value='" + key + "'>" + value + "</option>");
        });
        $("#dynamicSelects").append(newSelect);
        KTApp.init();
        }
        });
        }
        MakeJson();
        }

        $(document).ready(function () {
        // Обработчик события выбора в первом селекте
        $("#firstSelect").on("change", function () {
        $('.OnlyNew').remove();
        ReCalcSelect($(this));
        });

       // $('#kt_account_profile_details_submit').attr('disabled','disabled');
        MakeJson();
        });

@endsection