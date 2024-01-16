@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
@include('layouts.datatable')
@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Schedule</h4>
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
                    <label>Organization Name</label>
                    <select name="organization" id="organization" class="form-control">
                        <option value="" selected disabled>Choose Organization</option>
                        @foreach($organization as $row)
                        <option value="{{ $row->id }}">{{ $row->nama }}</option>
                        @endforeach
                    </select>

                    <!-- <label>Versi Jadual Waktu</label>
                    <input type="text" name="version" id="version" class="form-control" readonly value=""> -->
                    <!-- <select name="version" id="version" class="form-control">
                        <option value="" selected disabled>Pilih Versi</option>
                        
                    </select> -->

                    <label>Class</label>
                    <select name="class" id="class" class="form-control">
                        <option value="" selected disabled>Choose Class</option>
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
                <a style="margin: 19px;" href="#" class="btn btn-primary" data-toggle="modal" data-target="#modelId"> <i class="fas fa-plus"></i> Add Schedule</a>
                <a style="margin: 19px;" href="#" class="btn btn-primary" data-toggle="modal" data-target="#modelId1"> <i class="fas fa-plus"></i> Import</a>
                <a style="margin: 19px;" href="#" class="btn btn-primary" data-toggle="modal" data-target="#modelId2"> <i class="fas fa-plus"></i> Manage Schedule</a>
                <!-- <a style="margin: 1px;" href="#" class="btn btn-success" data-toggle="modal" data-target="#modelId2"> <i class="fas fa-plus"></i> Export</a> -->
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
                            <tr><th>Monday</th></tr>
                            <tr><th>Tuesday</th></tr>
                            <tr><th>Wednesday</th></tr>
                            <tr><th>Thursday</th></tr>
                            <tr><th>Friday</th></tr>
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
                                <input type="number" name="no_of_slot" id="no_of_slot" min=1 class="form-control" value =1>
                            </div>
                            <div class="form-group">
                                <label>Time Of Slot (Minutes)</label>
                                <input type="number" name="time_of_slot" id="time_of_slot" min=1 class="form-control" value=1>
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
                                <label>Time Off Slot</label>
                                <input type="text" name="time_off" id="time_off_text" placeholder= "1,2,4..." class="form-control">
                            </div>
                            <!-- <div class="form-group">
                                <label>Teacher Maximum Slot Per Week</label>
                                <input type="number" name="maxslot" id="maxslot" min=0 class="form-control">
                            </div>
                            <input type="hidden" name="organization_id" class="organization_id" value="0"> -->

                            <div class="form-group">
                                <label>Teacher Maximum Slot Per Day</label>
                                <input type="number" name="maxslot" id="maxslot" min=1 class="form-control" value=1>
                            </div>
                            <input type="hidden" name="organization_id" class="organization_id" value="0">

                            <div class="form-group">
                                <label>Teacher Maximum Relief Slot Per Day</label>
                                <input type="number" name="maxRelief" id="maxrelief" min=1 class="form-control" value=1>
                            </div>
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
                        <h5 class="modal-title">Import Schedule</h5>
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
                                    @if ($row->status == 1)
                                        <option value="{{ $row->id }}">{{ $row->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                            </div>
                            <div class="form-group">
                                <label>Description</label>
                                <textarea class="form-control" name="desc" id="desc" cols="30" rows="3" placeholder="Description"></textarea>
                            </div>

                            <div class="form-group">
                            <input type="radio"  name="new_version" value="1" checked>
                            <label for="html">Add as new version</label><br>
                            <input type="radio"  name="new_version" value="0">
                            <label for="css">Continue with current version (Exist data will not be overwritten)</label><br>

                           
                            </div>
                            <div class="form-group">
                                <input type="file" name="file" required><br>
                                <input type="checkbox"  name="autoInsert" checked>
                            <label >Automatic insert data of class, teacher and subject if not exist</label>
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

<!-- Modal 2-->
<div class="modal fade" id="modelId2" tabindex="-1" role="dialog" aria-labelledby="modelTitleId2" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Manage Schedule</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('schedule.updateSchedule') }}" method="post" enctype="multipart/form-data">
                        <div class="modal-body">
                            {{ csrf_field() }}
                            <div class="form-group">
                            <label>Choose Schedule Name</label>
                            <select name="schedule_id" id="update_schedule" class="form-control">
                                <option value="" selected disabled>Choose Schedule Name</option>
                                @foreach($schedule as $row)
                                <option value="{{ $row->id }}">{{ $row->name }}</option>
                                @endforeach
                                
                                <!-- <div>
                                    <input type="text"> </input>
                                </div> -->
                            </select>
                            <div>
                                <label id="version_count"></label>
                                <br>
                                <label id="schedule_desc"></label>
                            </div>
                            </div>
                            <div class="form-group">
                                    <label>Status:</label>
                                    <div class="form-check">
                                        <input type="radio" class="form-check-input" name ="schedule_status" id ="radioEnable" value ="true">
                                        <label class="form-check-label" for="radioConfirmed" >Active</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" class="form-check-input" name ="schedule_status"  id="radioDisable" value ="false">
                                        <label class="form-check-label" for="radioPending">Inactive</label>
                                    </div>
                                </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">Save Changes</button>
                                
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
        $('#update_schedule').change(function () {
            var selectedScheduleId = $(this).val();
            
           //console.log(selectedScheduleId);
            // Make an AJAX call
            $.ajax({
                type: 'GET',
                url: '{{ route("schedule.getScheduleStatus", ":id") }}'.replace(':id', selectedScheduleId), // Replace with your actual AJAX endpoint
                success: function (response) {
                    // Assuming the response contains a 'status' field
                    // and it can be either 'Confirmed' or 'Pending'
                    $('#version_count').text('Version Count: '+ response.version_count);
                    $('#schedule_desc').text('Desc: '+ response.desc);
                    console.log(response);
                    if (response.schedule_status ==  1) {
                        // Check the Confirmed radio button
                        $('#radioEnable').prop('checked', true);
                    } else {
                        // Check the Pending radio button
                        $('#radioDisable').prop('checked', true);
                    }
                   // console.error(response.schedule_status);
                    // Add more conditions if needed for other statuses
                },
                error: function (xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        });

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

        if ($("#organization").val() != "") {
            $("#organization").prop("selectedIndex", 1).trigger('change');
        }

        $('#class').change(function () {
        var class_id = $("#class option:selected").val();
        var urlLink = '{{ route("schedule.getScheduleView", ":classId") }}';
        urlLink = urlLink.replace(':classId', class_id);

        $.ajax({
            url: urlLink,
            type: 'GET',
            success: function (response) {
                var slots = response.slots;
                var timeoff = response.time_off;
                var maxDay = Math.max(...slots.map(slot => slot.day));
                var minDay = Math.min(...slots.map(slot => slot.day));
                var maxSlot = Math.max(...slots.map(slot => slot.slot));
                var minSlot = Math.min(...slots.map(slot => slot.slot));
                var weekday = [
                    'SUNDAY', 'MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY', 'SATURDAY', 'SUNDAY',
                ];

                $('#scheduleTable thead').empty(); // Clear existing thead

                // Create time slots in thead
                var timeRow = $('<tr style="text-align:center"></tr>');
                timeRow.append('<th></th>'); // Empty cell for days
                for (var slotIndex = minSlot; slotIndex <= maxSlot; slotIndex++) {
                    timeRow.append('<th>Slot ' + slotIndex + '<br>' + getSlotTime(slots, slotIndex) + '</th>');
                }
                $('#scheduleTable thead').append(timeRow);

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
                    var slot_array = slots.filter(s => s.day === dayIndex && s.slot === slotIndex);
                    var cellText ='';
                    var isBreak = timeoff.find(s => s.slot === slotIndex && (s.day ==null || s.day.find(d=>d ==dayIndex))) !== undefined;
                        //console.log(isBreak); 
                    if(isBreak){
                        
                        var text ='BREAK';
                        var break1 =timeoff.find(s => s.slot === slotIndex && (s.day ==null || s.day ==dayIndex));
                        if(break1?.desc){
                            text = break1.desc;
                        }
                        if(break1?.duration){
                            text += '<br>'+break1.duration+' min'
                        }
                        
                        cellText += text + '<br>';
                    }
                    slot_array.forEach((slot, index) => {
                       
                        cellText += slot.subject + ' - ' + slot.teacher ;
                            if (index < slot_array.length - 1) {
                                cellText += '<br>';
                            }
                       
                    });
                    
                        cell.html(cellText.trim());
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

    // ... (Rest of your code)

    // Function to get slot time based on slot index
    function getSlotTime(slots, slotIndex) {
        var slot = slots.find(s => s.slot === slotIndex);
        console.log(slot);
        if (slot && slot.time) {
            var startTime = moment(slot.time, 'HH:mm:ss');
            var endTime = moment(startTime).add(slot.time_of_slot, 'minutes');
            return startTime.format('HH:mm') + ' - ' + endTime.format('HH:mm');
        }
        return '';
    }

//         $('#class').change(function() {
//         var class_id = $("#class option:selected").val();
//         var urlLink = '{{ route("schedule.getScheduleView", ":classId") }}';
//         urlLink = urlLink.replace(':classId', class_id);

//     $.ajax({
//         url: urlLink,
//         type: 'GET',
//         success: function(response) {
//             var slots = response.slots;
//             var timeoff = response.time_off;
//             console.log(timeoff);
//             var maxDay = Math.max(...slots.map(slot => slot.day));
//             var minDay = Math.min(...slots.map(slot => slot.day));
//             var maxSlot = Math.max(...slots.map(slot => slot.slot));
//             var minSlot = Math.min(...slots.map(slot => slot.slot));
//             var weekday = [
//                 'AHAD', 'ISNIN', 'SELASA', 'RABU', 'KHAMIS', 'JUMAAT', 'SABTU', 'AHAD',
//             ];

//             $('#scheduleTable tbody').empty();

//             // Create rows for each day
//             for (var dayIndex = minDay; dayIndex <= maxDay; dayIndex++) {
//                 var row = $('<tr></tr>');
//                 var daycell = $('<td></td>');
//                 daycell.text(weekday[dayIndex]);
//                 row.append(daycell);
//                 // Create cells for each slot
//                 for (var slotIndex = minSlot; slotIndex <= maxSlot; slotIndex++) {
//                     var cell = $('<td></td>');
//                     var slot = slots.find(s => s.day === dayIndex && s.slot === slotIndex);
//                     var isBreak = timeoff.find(s => s.slot === slotIndex && (s.day ==null || s.day.find(d=>d ==dayIndex))) !== undefined;
//                     //console.log(isBreak); 
//                     if (slot) {
//                         cell.text(slot.subject + ' - ' + slot.teacher);
//                     }
//                     else if(isBreak){
                        
//                         var text ='REHAT';
//                         var break1 =timeoff.find(s => s.slot === slotIndex && (s.day ==null || s.day ==dayIndex));
//                         if(break1?.desc){
//                             text = break1.desc;
//                         }
//                         if(break1?.duration){
//                             text += '\n'+break1.duration+' min'
//                         }
                        
//                         cell.text(text);
//                     }

//                     row.append(cell);
//                 }

//                 $('#scheduleTable tbody').append(row);
//             }
//         },
//         error: function(xhr, status, error) {
//             console.log(error);
//         }
//     });
// });

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


    });

</script>
@endsection