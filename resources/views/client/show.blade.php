
    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">


        <div class="" id="kt_post">
            <div id="kt_content_container" class="container-xxl">

                <div class="card mb-5 mb-xl-8">


                    <div class="card-body py-3">

                        <div class="table-responsive">
                            @if(@sizeof($rez))
                            <table class="table table-row-bordered table-row-gray-100 align-middle gs-0 gy-3">
                                <tr class="">

                                        <th>
                                            Date
                                        </th>
                                    <th>
                                        MB (megabytes)
                                    </th>

                                </tr>

                                <tbody>
                                @foreach (@$rez as $k=> $row)
                                    <tr>

                                            <td>
                                                {{ $k }}

                                            </td>

                                        <td>
                                            {{ $row }}

                                        </td>

                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                                @endif
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
