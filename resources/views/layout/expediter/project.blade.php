
@extends('home')

@section('sidebar')
    @include('layout.expediter.includes.sidebar')
@endsection

@section('topbar')
    @include('layout.expediter.includes.topbar')
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="scrollable">
        <div class="card">
            <div class="card-header">
                <h3><b id="project_name">{{ $data["project"][0]["project_name"] }}</b></h3>
            </div>
            <div class="card-body">
                @if(Session::has('success'))
                <div class="alert alert-success">
                    {{ Session::get('success')}}
                </div>
                @endif
                <div class="overflow-auto">
                    <div class="my-3 h-100" id="chart_div"></div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="project_name_input">Project Name</label>
                            <input type="text" class="form-control" id="project_name_input" placeholder="Project Name" value="{{ $data['project'][0]['project_name'] }}" readonly @if($data['project'][0]['project_status'] == "approved") disabled @endif>
                        </div>
                        <div class="form-group">
                            <label for="project_address">Project Address</label>
                            <input type="text" class="form-control" id="project_address_input" placeholder="Project Address" value="{{ $data['project'][0]['project_address'] }}" readonly @if($data['project'][0]['project_status'] == "approved") disabled @endif> 
                        </div>
                        <div class="form-group">
                            <label for="client_id">Select Client</label>
                            <select name="client_id" id="client_id" class="custom-select" disabled>
                                @foreach($data['clients'] as $client)
                                    <option value="{{ $client['id'] }}" @if($data['project'][0]['client_id'] == $client['id']) selected @endif>{{ $client["client_f_name"] . " " . $client["client_l_name"]}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <h3>Project Supplies</h3>
                <table class="table table-striped" id="supply_table">
                    <thead>
                        <th>Supply ID</th>
                        <th>Supply Name</th>
                        <th>Supply Description</th>
                        <th>Supply Count</th>
                        <th>Action</th>
                    </thead>
                    <tbody>

                    </tbody>
                    <tfoot>
                        <th>Supply ID</th>
                        <th>Supply Name</th>
                        <th>Supply Description</th>
                        <th>Supply Count</th>
                        <th>Action</th>
                    </tfoot>
                </table>
                <hr>
            </div>
        </div>
        <div class="modal fade" id="supply_modal" tabindex="-1" role="dialog" aria-labelledby="supply_modaltitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="supply_modaltitle">Update Supply</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @if($data['project'][0]['project_status'] == "approved") disabled @endif>
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="supply_form">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col">
                                <p>Name: <b id="supply_name"></b></p>
                                <p>Supply Needed: <b id="supply_count"></b></p>
                            </div>
                            <div class="col">
                                <p>Description:</p>
                                <p id="supply_desc"></p>
                            </div>
                        </div>
                        <hr><input type="hidden" id="supply_id">
                        <div class="row">
                            <div class="form-group col">
                                <label for="">Store Purchased</label>
                                <input type="text" id="store_purchased" required class="form-control" @if($data['project'][0]['project_status'] == 'approved') readonly @endif>
                            </div>
                            <div class="form-group col">
                                <label for="">Supply Count</label>
                                <input type="number" step="any" id="supply_count_purchased" required class="form-control" @if($data['project'][0]['project_status'] == 'approved') readonly @endif>
                            </div>
                            <div class="form-group col">
                                <label for="">Supply Price</label>
                                <input type="number" step="any" id="supply_price" required class="form-control" @if($data['project'][0]['project_status'] == 'approved') readonly @endif>
                            </div>
                        </div>
                        <hr>
                        <h3>Supply Details</h3>
                        <table class="table table-striped" id="supply_purchased_table">
                            <thead>
                                <th>ID</th>
                                <th>Store Name</th>
                                <th>Supply Purchased Count</th>
                                <th>Supply Price</th>
                                <th>Total</th>
                                <th>Action</th>
                            </thead>
                            <tbody>

                            </tbody>
                            <tfoot>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th>Total</th>
                                <th></th>
                                <th>Action</th>
                            </tfoot>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>

                </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script src="https://cdn.datatables.net/plug-ins/1.10.24/api/sum().js"></script>
<script type="text/javascript">
    var data, chart, db_data, task_counter = 0, $counter = 0;
    var supply_table = $("#supply_table").DataTable({
        "drawCallback": function () {
            // var api = this.api();
            // $( api.table().footer() ).html(
            //     '<tr>' +
            //         '<td></td>' +
            //         '<td></td>' +
            //         '<td></td>' +
            //         '<td>Total</td>' +
            //         '<td>' + api.column( 4, {page:'current'} ).data().sum() + '</td>' +
            //         '<td></td>' +
            //     '</tr>'
            // );
        },
        "columnDefs": [ {
            "targets": -1,
            "data": null,
            "defaultContent": "<button class='btn btn-success' @if($data['project'][0]['project_status'] == 'approved') disabled @endif>Update</button>"
        } ]
    });
    var supply_purchased_table = $("#supply_purchased_table").DataTable({
        "drawCallback": function () {
            var api = this.api();
            $( api.table().footer() ).html(
                '<tr>' +
                    '<td></td>' +
                    '<td></td>' +
                    '<td></td>' +
                    '<td>Total</td>' +
                    '<td>' + api.column( 4, {page:'current'} ).data().sum() + '</td>' +
                    '<td></td>' +
                '</tr>'
            );
        },
        "columnDefs": [ {
            "targets": -1,
            "data": null,
            "defaultContent": "<button type='button' class='btn btn-danger' @if($data['project'][0]['project_status'] == 'approved') disabled @endif>Delete</button>"
        } ]
    });

    function update_supply_details(){
        supply_purchased_table.clear().draw();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{route('supply.getsupply')}}",
            method: "POST",
            data:{
                "id" : $("#supply_id").val()
            },
            success: function(e){
                e = JSON.parse(e);
                e.forEach(async (element, index, array) => {
                    supply_purchased_table.row.add([
                        element["id"],
                        element["store_purchased"],
                        element["supply_count_purchased"],
                        element["supply_price"],
                        element["supply_price"] * element["supply_count_purchased"],
                    ]).draw();
                });
            }
        });
    }

    $('#supply_table tbody').on( 'click', 'button', function () {
        var rowId = supply_table.row( $(this).parents('tr') ).index();
        $("#supply_id").val(supply_table.row(rowId).data()[0]);
        $("#supply_name").text(supply_table.row(rowId).data()[1]);
        $("#supply_count").text(supply_table.row(rowId).data()[3]);
        $("#supply_desc").text(supply_table.row(rowId).data()[2]);
        update_supply_details();
        $("#supply_modal").modal('show');
    });

    $('#supply_purchased_table tbody').on( 'click', 'button', function () {
        var rowId = supply_purchased_table.row( $(this).parents('tr') ).index();
        var id = supply_purchased_table.row(rowId).data()[0];
        supply_table.row(rowId).remove().draw();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{route('supply.removesupply')}}",
            method: "POST",
            data:{
                "id" : id
            },
            success: function(e){
                e = JSON.parse(e);
                alert(e["alert"]);
            }
        });
    });

    $("#supply_form").submit(function(e){
        e.preventDefault();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{route('supply.addsupply')}}",
            method: "POST",
            data:{
                "supply_id" : $("#supply_id").val(),
                "store_purchased" : $("#store_purchased").val(),
                "supply_count_purchased" : $("#supply_count_purchased").val(),
                "supply_price" : $("#supply_price").val()
            },
            success: function(e){
                e = JSON.parse(e);
                update_supply_details();
                alert(e["alert"]);
            }
        });
    });
    function init_data(update = false){
        if(update){
            supply_table.clear();
        }
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{route('getdata')}}",
            method: "POST",
            data:{
                "project_id" : "{{ $data['project'][0]['id'] }}"
            },
            success: function(e){
                db_data = JSON.parse(e);
                drawChart();
            }
        });
    }
    function drawChart() {
        data = new google.visualization.DataTable();
        data.addColumn('string', 'Task ID');
        data.addColumn('string', 'Task Name');
        data.addColumn('string', 'Priority');
        data.addColumn('date', 'Start Date');
        data.addColumn('date', 'End Date');
        data.addColumn('number', 'Duration');
        data.addColumn('number', 'Percent Complete');
        data.addColumn('string', 'Dependencies');
        chart = new google.visualization.Gantt(document.getElementById('chart_div'));
        var itemsProcessed = 0;
        db_data["plans"].forEach(async (element, index, array) => {
            $dependency = element["plan_dependency"];
            if($dependency == "plan-null" || $dependency == "null"){
                $dependency = null;
            }
            var plan_id = "plan-" + index;
            data.addRow([
                plan_id ,
                element["plan_name"],
                element["plan_priority"],
                new Date(element["plan_date_start"]),
                new Date(element["plan_date_end"]),
                1,
                0,
                $dependency
            ]);
            $("#plan_parent").append(new Option(element["plan_name"], index));
        });
        db_data["supplies"].forEach(async (element) => {
            supply_table.row.add([
                element["id"],
                element["supply_name"],
                element["supply_description"],
                element["purchased"] + "/" + element["supply_count"],
            ]).draw();
        });
        var trackHeight = 40;
        var options = {
            height: data.getNumberOfRows() * trackHeight,
            width: "100%",
            hAxis: {
                textStyle: {
                    fontName: ["RobotoCondensedRegular"]
                }
            },
            gantt: {
                labelStyle: {
                fontName: ["RobotoCondensedRegular"],
                fontSize: 12,
                color: '#757575',
                },
                trackHeight: trackHeight
            }
        };
        chart.draw(data, options);
    }
    $(document).ready(function(){
        google.charts.load('current', {'packages':['gantt']});
        google.charts.setOnLoadCallback(init_data);
    });
</script>
@endsection