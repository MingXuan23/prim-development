@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
@include('layouts.datatable')
@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Jadual</h4>
            <!-- <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Welcome to Veltrix Dashboard</li>
            </ol> -->
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">

            {{csrf_field()}}
            <div class="card-body">

                <div class="form-group">
                    <label>Nama Organisasi</label>
                    <select name="organization" id="organization" class="form-control">
                        <option value="" selected disabled>Pilih Organisasi</option>
                        @foreach($organization as $row)
                        <option value="{{ $row->id }}">{{ $row->nama }}</option>
                        @endforeach
                    </select>

                    <!-- <label>Versi Jadual Waktu</label>
                    <input type="text" name="version" id="version" class="form-control" readonly value=""> -->
                    <!-- <select name="version" id="version" class="form-control">
                        <option value="" selected disabled>Pilih Versi</option>
                        
                    </select> -->

                    <label>Kelas</label>
                    <select name="class" id="class" class="form-control">
                        <option value="" selected disabled>Pilih Kelas</option>
                        @foreach($classes as $row)
                        <option value="{{ $row->id }}">{{ $row->nama }}</option>
                        @endforeach
                    </select>
                </div>


            </div>

            {{-- <div class="">
                <button onclick="filter()" style="float: right" type="submit" class="btn btn-primary"><i
                        class="fa fa-search"></i>
                    Tapis</button>
            </div> --}}

        </div>
    </div>

    <div class="col-md-12">
        <div class="card">
            {{-- <div class="card-header">List Of Applications</div> --}}
            <div>
                <a style="margin: 19px;" href="#" class="btn btn-primary" data-toggle="modal" data-target="#modelId"> <i class="fas fa-plus"></i> Tambah Jadual</a>
                <a style="margin: 19px;" href="#" class="btn btn-primary" data-toggle="modal" data-target="#modelId1"> <i class="fas fa-plus"></i> Import</a>
                <a style="margin: 1px;" href="#" class="btn btn-success" data-toggle="modal" data-target="#modelId2"> <i class="fas fa-plus"></i> Export</a>
                <!-- <a style="margin: 1px;" href=" {{ route('exportteacher') }}" class="btn btn-success"> <i
                        class="fas fa-plus"></i> Export</a> -->
                <!-- <a style="margin: 19px; float: right;" href="{{ route('subject.create') }}" class="btn btn-primary"> <i class="fas fa-plus"></i> Tambah Subjek</a> -->
            </div>

            <div class="card-body">

                @if(count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                        <li>{{$error}}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                @if(\Session::has('success'))
                <div class="alert alert-success">
                    <p>{{ \Session::get('success') }}</p>
                </div>
                @endif
                @if(\Session::has('error'))
                <div class="alert alert-danger">
                    <p>{{ \Session::get('error') }}</p>
                </div>
                @endif

                <div class="flash-message"></div>

                <div class="table-responsive">
                    <table id="scheduleTable" class="table table-bordered table-striped dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr style="text-align:center">
                                <th></th>
                                <th>Slot 1</th>
                                <th>Slot 2</th>
                                <th>Slot 3</th>
                                <th>Slot 4</th>
                                <th>Slot 5</th>
                                <th>Slot 6</th>
                                <th>Slot 7</th>
                                <th>Slot 8</th>
                                <th>Slot 9</th>
                                <th>Slot 10</th>
                                <th>Slot 11</th>
                                <th>Slot 12</th>
                            </tr>
                            <tbody>
                            <tr><th>Isnin</th></tr>
                            <tr><th>Selasa</th></tr>
                            <tr><th>Rabu</th></tr>
                            <tr><th>Khamis</th></tr>
                            <tr><th>Jumaat</th></tr>
                            </tbody>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        <!-- {{-- confirmation delete modal --}}
        <div id="deleteConfirmationModal" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Padam Subjek</h4>
                    </div>
                    <div class="modal-body">
                        Adakah anda pasti?
                    </div>
                    <div class="modal-footer">
                        <button type="button" data-dismiss="modal" class="btn btn-primary" id="delete" name="delete">Padam</button>
                        <button type="button" data-dismiss="modal" class="btn">Batal</button>
                    </div>
                </div>
            </div>
        </div>
        {{-- end confirmation delete modal --}} -->

        <!-- <div class="modal fade" id="modelId1" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Export Subjek</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    {{-- {{ route('exportteacher') }} --}}
                    <form action="{{ route('exportteacher') }}" method="post">
                        <div class="modal-body">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label>Organisasi</label>
                                <select name="organ" id="organ" class="form-control">
                                    @foreach($organization as $row)
                                    <option value="{{ $row->id }}" selected>{{ $row->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="modal-footer">
                                <button id="buttonExport" type="submit" class="btn btn-primary">Export</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div> -->

        <!-- Modal -->
        <div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Import Maklumat Jadual</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('importSchedule') }}" method="post" enctype="multipart/form-data">
                        <div class="modal-body">

                            {{ csrf_field() }}
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" name="name" id="name" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Number Of Slot</label>
                                <input type="number" name="no_of_slot" id="no_of_slot" min=0 class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Time Of Slot (Minutes)</label>
                                <input type="number" name="time_of_slot" id="time_of_slot" min=0 class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Start Time</label>
                                <input type="time" name="starttime" id="starttime" class="form-control">
                            </div>
                            <div class="form-row">
                            <div class="form-group col-md-5">
                                <div>
                                <label>Day Of Week</label>
                                </div>
                            </div>
                                <div class="form-group col-md-4">
                                    <input type="checkbox" id="select-all-days">
                                    <label for="select-all-days">Select All</label>
                                <div class="form-control">
                                    <input type="checkbox" name="day[]" id="monday" value ="1" >
                                    <label for="monday">Monday</label>
                                </div>
                                <div class="form-control">
                                    <input type="checkbox" name="day[]" id="tuesday" value ="2">
                                    <label for="tuesday">Tuesday</label>
                                </div>
                                <div class="form-control">
                                    <input type="checkbox" name="day[]" id="wednesday" value ="3">
                                    <label for="wednesday">Wednesday</label>
                                </div>
                                <div class="form-control">
                                    <input type="checkbox" name="day[]" id="thursday" value ="4">
                                    <label for="thursday">Thursday</label>
                                </div>
                                <div class="form-control">
                                    <input type="checkbox" name="day[]" id="friday" value ="5">
                                    <label for="friday">Friday</label>
                                </div>
                                <div class="form-control">
                                    <input type="checkbox" name="day[]" id="saturday" value ="6">
                                    <label for="saturday">Saturday</label>
                                </div>
                                <div class="form-control">
                                    <input type="checkbox" name="day[]" id="sunday" value ="7">
                                    <label for="sunday">Sunday</label>
                                </div>
                            </div>
                            </div>
                            <!-- <div class="form-group">
                                <label>Time Off</label>
                                <input type="time" name="offtime" id="offtime">
                            </div> -->
                            <div class="form-group">
                                <label>Teacher Maximum Slot</label>
                                <input type="number" name="maxslot" id="maxslot" min=0 class="form-control">
                            </div>
                            <input type="hidden" name="organization_id" class="organization_id" value="0">

                            <!-- <div class="form-group">
                                <input type="file" name="file" required>
                            </div> -->

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal 1-->
<div class="modal fade" id="modelId1" tabindex="-1" role="dialog" aria-labelledby="modelTitleId1" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Import Jadual</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('importScheduleSubject') }}" method="post" enctype="multipart/form-data">
                        <div class="modal-body">
                            {{ csrf_field() }}
                            <div class="form-group">
                            <label>Choose Schedule Name</label>
                            <select name="schedule_id" id="schedule_id" class="form-control">
                                <option value="" selected disabled>Choose Schedule Name</option>
                                @foreach($schedule as $row)
                                <option value="{{ $row->id }}">{{ $row->name }}</option>
                                @endforeach
                            </select>
                            </div>
                            <div class="form-group">
                                <label>Description</label>
                                <textarea class="form-control" name="desc" id="desc" cols="30" rows="3" placeholder="Description"></textarea>
                            </div>
                            <input type="hidden" name="organization_id" class="organization_id" value="0">
                            <div class="form-group">
                                <input type="file" name="file" required>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Import</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection


@section('script')
<!-- Peity chart-->
<script src="{{ URL::asset('assets/libs/peity/peity.min.js')}}"></script>

<!-- Plugin Js-->
<script src="{{ URL::asset('assets/libs/chartist/chartist.min.js')}}"></script>

<script src="{{ URL::asset('assets/js/pages/dashboard.init.js')}}"></script>
-=
<script>
    $(document).ready(function() {

        var scheduleTable;
        
        if ($("#organization").val() != "") {
            $("#organization").prop("selectedIndex", 1).trigger('change');
        }

        $('#organization').change(function() {
            var organizationid = $("#organization option:selected").val();
            $('.organization_id').val(organizationid);
            var urlLink = '{{ route("schedule.getVersion", ":organizationid") }}';
            urlLink = urlLink.replace(':organizationid', organizationid);
            $.ajax({
                url: urlLink,
                type: 'GET',
                success: function(response) {
                    var versions = response.versionList;
                    console.log(versions);
                    // // Clear existing options
                    // $('#version').empty();

                    // // Add a default option
                    // $('#version').append('<option value="" selected disabled>Pilih Versi</option>');

                    // // Add options based on the response
                    // versions.forEach(function(version) {
                    //     $('#version').append('<option value="' + version.version_id + '">' + version.code + '</option>');
                    // });

                    // // Refresh the styling of the select (if you're using a library like Select2)
                    // $('#version').trigger('change');
                },
                error: function(xhr, status, error) {
                console.log(error);
                }
            });
        });

        $('#class').change(function() {
        var class_id = $("#class option:selected").val();
        var urlLink = '{{ route("schedule.getScheduleView", ":classId") }}';
        urlLink = urlLink.replace(':classId', class_id);

    $.ajax({
        url: urlLink,
        type: 'GET',
        success: function(response) {
            var slots = response.slots;
            var timeoff = response.time_off;
            console.log(timeoff);
            var maxDay = Math.max(...slots.map(slot => slot.day));
            var minDay = Math.min(...slots.map(slot => slot.day));
            var maxSlot = Math.max(...slots.map(slot => slot.slot));
            var minSlot = Math.min(...slots.map(slot => slot.slot));
            var weekday = [
                'AHAD', 'ISNIN', 'SELASA', 'RABU', 'KHAMIS', 'JUMAAT', 'SABTU', 'AHAD',
            ];

            $('#scheduleTable tbody').empty();

            // Create rows for each day
            for (var dayIndex = minDay; dayIndex <= maxDay; dayIndex++) {
                var row = $('<tr></tr>');
                var daycell = $('<td></td>');
                daycell.text(weekday[dayIndex]);
                row.append(daycell);
                // Create cells for each slot
                for (var slotIndex = minSlot; slotIndex <= maxSlot; slotIndex++) {
                    var cell = $('<td></td>');
                    var slot = slots.find(s => s.day === dayIndex && s.slot === slotIndex);
                    var isBreak = timeoff.find(s => s.slot === slotIndex && (s.day ==null || s.day.find(d=>d ==dayIndex))) !== undefined;
                    //console.log(isBreak); 
                    if (slot) {
                        cell.text(slot.subject + ' - ' + slot.teacher);
                    }
                    else if(isBreak){
                        
                        var text ='REHAT';
                        var break1 =timeoff.find(s => s.slot === slotIndex && (s.day ==null || s.day ==dayIndex));
                        if(break1?.desc){
                            text = break1.desc;
                        }
                        if(break1?.duration){
                            text += '\n'+break1.duration+' min'
                        }
                        
                        cell.text(text);
                    }

                    row.append(cell);
                }

                $('#scheduleTable tbody').append(row);
            }
        },
        error: function(xhr, status, error) {
            console.log(error);
        }
    });
});

// Rest of your code...
    });

    document.getElementById('select-all-days').addEventListener('change', function () {
        var checkboxes = document.querySelectorAll('[name^="day"]');
        checkboxes.forEach(function (checkbox) {
            checkbox.checked = document.getElementById('select-all-days').checked;
        });
    });

    // Listen for changes in individual checkboxes to uncheck "Select All" if needed
    var dayCheckboxes = document.querySelectorAll('[name^="day"]');
    dayCheckboxes.forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            document.getElementById('select-all-days').checked = false;
        });
    });
</script>
@endsection