
                        <div class="table-responsive">
                            <table class="table table-row-bordered table-row-gray-100 align-middle gs-0 gy-3">
                                <thead>
                                <tr class="">
                                        <th>Asset</th>
                                    <th>free</th>
                                    <th>locked</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($rows as $row)
                                <tr>
                                    <td>{{$row->asset}}</td>
                                    <td>{{$row->free}}</td>
                                    <td>{{$row->locked}}</td>
                                                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

