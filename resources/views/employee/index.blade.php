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
                    @if ($settings['isAdd'])
                    <a href="{{ route('employee.create') }}" class="btn btn-sm btn-primary">{{ $settings['buttons']['add'] }}</a>
                    @endif
                </div>
            </div>
        </div>
        <div class="" id="kt_post">
            <div id="kt_content_container" class="container-xxl">
                <div class="card mb-5 mb-xl-8">
                    <div class="card-header border-0 pt-5">

                        <div class="col-md-12">
                            {{ Form::open(['route' => ['employee.index'],'method' => 'get']) }}

                            <div class="row">
                                <div class="col-md-10">
                            <div class="row">


                            @foreach ($settings['form'] as $fname => $field)

                                <div class="col">
                                        @if($field=='text')
                                            {{ Form::text($fname, $request->$fname, ['placeholder'=> $settings['attr'][$fname], 'class'=>'form-control']) }}
                                        @endif
                                        @if(is_array($field))
                                            {{ Form::select($request->$fname, $field[0], $request->$fname,$field[1]) }}
                                        @endif

                                    </div>
                            @endforeach

                            </div>
                                </div>

                                <div class="col-md-2 right">
                                    <div class="btn-group" role="group" aria-label="Basic example">
                            <a href="{{ route('employee.index') }}" class="btn btn-danger">Cancel</a><button type="submit" class="btn btn-primary">
                                <span class="indicator-label"> Search </span></span>
                            </button>
                                    </div>

                        </div>

                            </div>
                            {{ Form::close() }}
            </div>
        </div>
                </div>
            </div>
        </div>
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-xxl">

                <div class="card mb-5 mb-xl-8">
                    <!--begin::Header-->
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bolder fs-3 mb-1">All</span>
                            <span class="text-muted mt-1 fw-bold fs-7">{{ $rows->count() }} records</span>
                        </h3>
                        <div class="card-toolbar">
                            <button type="button" class="btn btn-sm btn-icon btn-color-primary btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                <i class="fas fa-list"></i>
                            </button>


                        </div>
                    </div>

                    <div class="card-body py-3">
                        @if ($message = Session::get('success'))
                            <div class="alert alert-success">
                                {{ $message }}
                            </div>
                        @endif
                        <div class="table-responsive">
                            <table class="table table-row-bordered table-row-gray-100 align-middle gs-0 gy-3">
                                <thead>
                                <tr class="fw-bolder text-muted">
                                    @if ($settings['isCheckbox'])
                                    <th class="w-25px">
                                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                                            <input class="form-check-input" type="checkbox" value="1" data-kt-check="true" data-kt-check-target=".widget-13-check">
                                        </div>
                                    </th>
                                    @endif
                                    @foreach ($settings['table'] as $k=>$t)
                                        <th>
                                            {{ $settings['attr'][$t] }}
                                        </th>
                                    @endforeach
                                    <th class="min-w-100px text-end">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($rows as $row)
                                <tr>
                                    @if ($settings['isCheckbox'])
                                    <td>
                                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                                            <input class="form-check-input widget-13-check" type="checkbox" value="1">
                                        </div>
                                    </td>
                                    @endif
                                    @foreach ($settings['table'] as $k=>$t)
                                    <td>
                                        {{ $row->$t }}
                                    </td>
                                    @endforeach

                                    <td class="text-end">
                                        <form action="{{ route('employee.destroy',$row->id) }}" method="POST">
                                            @if ($settings['isShow'])
                                        <a href="{{ route('employee.show',$row->id) }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                                            <i class="fas fa-file"></i>
                                        </a>
                                            @endif
                                                @if ($settings['isEdit'])
                                        <a href="{{ route('employee.edit',$row->id) }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                                @endif
                                            @csrf
                                            @method('DELETE')

                                                @if ($settings['isDel'] && $row->id!=1)
                                            <button type="submit" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm"><i class="fas fa-trash"></i></button>
                                                @endif
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                            {{ $rows->appends($request->all())->links('base.pages') }}
                    </div>
                </div>

    </div>
        </div>
    </div>

@endsection