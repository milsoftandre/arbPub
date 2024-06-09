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
                    <div class="card-header border-0"><div class="card-title m-0"><h3 class="fw-bolder m-0">{{__('bot.add')}}</h3></div></div>

                        {{ Form::open(['route' => 'bot.store']) }}

                    <div class="card-body py-3">

                        @foreach ($settings['form'] as $fname => $field)
                        <div class="row mb-6 pt-2">
                            <label class="col-lg-4 col-form-label fw-bold fs-6">
                                <span>{{ $settings['attr'][$fname] }}</span>
                            </label>
                            <div class="col-lg-8 fv-row">
                                @if($field=='textarea')
                                    {{ Form::textarea($fname, null, ['placeholder'=> $settings['attr'][$fname], 'class'=>'form-control form-control-lg form-control-solid', 'required'=>'required']) }}
                                @endif
                                @if($field=='text')
                                        {{ Form::text($fname, null, ['placeholder'=> $settings['attr'][$fname], 'class'=>'form-control form-control-lg form-control-solid', 'required'=>'required']) }}
                                @endif
                                    @if($field=='time')
                                        {{ Form::input('time',$fname, null, ['placeholder'=> $settings['attr'][$fname], 'class'=>'form-control form-control-lg form-control-solid', 'required'=>'required']) }}
                                    @endif
                                    @if($field=='password')
                                        {{ Form::password($fname, ['placeholder'=> $settings['attr'][$fname], 'class'=>'form-control form-control-lg form-control-solid']) }}
                                            </div>
                        </div><div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-bold fs-6">
                                <span>{{__('bot.rpwd')}}</span>
                            </label>
                                            <div class="col-lg-8 fv-row">
                                            <input type="password" class="form-control form-control-lg form-control-solid" name="password_confirmation">
                                    @endif
                                        @if(is_array($field))
                                            {{ Form::select($fname, $field[0], null,$field[1]) }}
                                                    @if($fname=='currency_pairs')

                                                    <div id="dynamicSelects">
                                                        <!-- Здесь будут динамически добавленные селекты -->
                                                    </div>
                                                    @endif

                                        @endif
                            </div>
                        </div>
                        @endforeach


                    </div>
                    <input name="pairsJson" id="pairsJson" type="hidden">
                    <div class="card-footer d-flex justify-content-end py-6 px-9">
                        <a href="{{ route('bot.index') }}" class="btn btn-white btn-active-light-primary me-2"> {{__('bot.cansel')}} </a>
                        <button type="submit" id="kt_account_profile_details_submit" class="btn btn-primary">
                            <span class="indicator-label"> {{__('bot.save')}} </span></span>
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
    elementsToRemove.remove();

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

        $('#kt_account_profile_details_submit').attr('disabled','disabled');
        });

@endsection