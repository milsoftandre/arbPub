@extends('base.base')

@section('pageTitle', $settings['title'])

@section('content')
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="toolbar" id="kt_toolbar">
            <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
                <div data-kt-swapper="true" data-kt-swapper-mode="prepend" data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}" class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                    <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">{{ $settings['title'] }}</h1>
                </div>
                <div class="d-flex align-items-center py-1">
                    <a href="{{ route('employee.create') }}" class="btn btn-sm btn-primary">{{ $settings['buttons']['add'] }}</a>
                </div>
            </div>
        </div>
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
                    <div class="card-header border-0"><div class="card-title m-0"><h3 class="fw-bolder m-0">Add</h3></div></div>

                        {{ Form::open(['route' => 'employee.store']) }}

                    <div class="card-body py-3">

                        @foreach ($settings['form'] as $fname => $field)
                        <div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-bold fs-6">
                                <span>{{ $settings['attr'][$fname] }}</span>
                            </label>
                            <div class="col-lg-8 fv-row">
                                @if($field=='text')
                                        {{ Form::text($fname, null, ['placeholder'=> $settings['attr'][$fname], 'class'=>'form-control form-control-lg form-control-solid']) }}
                                @endif
                                    @if($field=='password')
                                        {{ Form::password($fname, ['placeholder'=> $settings['attr'][$fname], 'class'=>'form-control form-control-lg form-control-solid']) }}
                                            </div>
                        </div><div class="row mb-6">
                            <label class="col-lg-4 col-form-label fw-bold fs-6">
                                <span>Повторите пароль</span>
                            </label>
                                            <div class="col-lg-8 fv-row">
                                            <input type="password" class="form-control form-control-lg form-control-solid" name="password_confirmation">
                                    @endif
                                        @if(is_array($field))
                                            {{ Form::select('size', $field[0], null,$field[1]) }}
                                        @endif
                            </div>
                        </div>
                        @endforeach


                    </div>
                    <div class="card-footer d-flex justify-content-end py-6 px-9">
                        <a href="{{ route('employee.index') }}" class="btn btn-white btn-active-light-primary me-2"> {{__('bot.cansel')}} </a>

                        <button type="submit" id="kt_account_profile_details_submit" class="btn btn-primary">
                            <span class="indicator-label"> Save </span></span>
                        </button>
                    </div>
                    {{ Form::close() }}
                </div>

            </div>
        </div>
    </div>

@endsection