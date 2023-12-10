@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/css/required-asterick.css')}}" rel="stylesheet">
{{-- <link href="{{ URL::asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet"> --}}
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<link rel="stylesheet" href="{{ URL::asset('assets/css/datatable.css')}}">
@include('layouts.datatable')
@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Relief Report</h4>
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
                </div>

                <div class="form-group">
                    <label>Tarikh</label>
                    <input type="text" value="" class="form-control" name="pickup_date" id="datepicker"  placeholder="Pilih tarikh" readonly required>
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

                <div class="total_report">
                    <div class="total_confirmed"></div>
                    <div class="total_pending"></div>
                    <div class="total_rejected"></div>
                </div>

                <div class="table-responsive">
                    <table id="reliefTable" class="table table-bordered table-striped dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr style="text-align:center">
                                <th>No </th>
                                <th>Kelas</th>
                                <th>Subjek</th>
                                <th>Slot</th>
                                <th>Guru Asal</th>
                                <th>Guru Ganti</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>

                <!-- <div class="row">
                    <a style="margin: 19px;" href="#" class="btn btn-primary ml-auto" data-toggle="modal" data-target="#modelId">
                        <i class="fas fa-plus"></i> Add Row
                    </a>
                </div>
                <div class="row">
                    <a style="margin: 10px;" class="btn btn-danger">
                        <i class="fas fa-plus"></i> Discard
                    </a>
                    <a style="margin: 10px;" class="btn btn-success">
                        <i class="fas fa-plus"></i> Confirm
                    </a>
                </div> -->

            </div>
        </div>

        <!-- Modal -->
        <!-- <div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Row</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="modal-body">

                            {{ csrf_field() }}
                            <div class="form-group">
                            <div class="form-group">
                                <label>Time of Leave</label>
                                <input type="radio" name="timeOfLeave" id="fullday" onclick="displaySelectTime()" checked> Full Day
                                <input type="radio" name="timeOfLeave" id="period" onclick="displaySelectTime()"> Period
                            </div>
                            
                            <div id="selectTime" style="display: none;">
                                <div class="form-group">
                                    <label>Start Time</label>
                                    <input type="time" name="starttime" id="starttime" class="form-control">
                                </div>
                                <div class="form-group" >
                                    <label>End Time</label>
                                    <input type="time" name="starttime" id="endtime" class="form-control">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="reason">Reason</label>
                                <select name="reason" id="reason" class="form-control">
                                    <option value="mc">MC</option>
                                    <option value="event">Event</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="note">Note</label>
                                <input type="text" name="note" id="note" placeholder="Enter your note here..." class="form-control">
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Add</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>  -->

    </div>
</div>


@endsection


@section('script')
<!-- Peity chart-->
<script src="{{ URL::asset('assets/libs/peity/peity.min.js')}}"></script>

<!-- Plugin Js-->
<script src="{{ URL::asset('assets/libs/chartist/chartist.min.js')}}"></script>

<script src="{{ URL::asset('assets/js/pages/dashboard.init.js')}}"></script>

<script>   
        var dates = []
    $(document).ready(function() {
        $("#datepicker").datepicker("setDate", new Date());
        dateOnChange();

        if ($("#organization").val() != "") {
            $("#organization").prop("selectedIndex", 1).trigger('change');
            // fetch_data($("#organization").val());
        }

        $('#organization').change(function() {
            var organizationid = $("#organization option:selected").val();
            $('#reliefTable').DataTable().destroy();
            // console.log(organizationid);
            // fetch_data(organizationid);
        });

        // csrf token for ajax
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.alert').delay(3000).fadeOut();

        $('#datepicker').change(function() {
        //    dateOnChange();
           fetchReliefData();
        })
        
        

        // Function to display relief data in the table
       
        // var table = $('#reliefTable');
        // console.log(table);

        // Initial fetch when the page loads
        fetchReliefData();

        });

        $("#datepicker").datepicker({
            minDate: '-1m',
            maxDate: '+1m',
            dateFormat: 'yy-mm-dd',
            dayNamesMin: ['Ahd', 'Isn', 'Sel', 'Rab', 'Kha', 'Jum', 'Sab'],
            beforeShowDay: editDays,
            defaultDate: 0, 
        })

        function fetchReliefData() {
            let date_val = $('#datepicker').val();
            // If date is empty, set it to today
            if (!date_val) {
                date_val = $.datepicker.formatDate('yy-mm-dd', new Date());
                $('#datepicker').datepicker('setDate', date_val); // Update datepicker value
            }
            console.log(date_val);
            $.ajax({
                url: '{{ route("schedule.getReliefReport") }}',
                type: 'POST',
                data: {
                    organization: $('#organization option:selected').val(), 
                    date: date_val,
                    // Replace with your organization ID
                },
                success: function (response) {
                    console.log(response); // Log the pending relief data
                    //console.log(response.available_teachers); // Log the available teachers data 
                    displayRelief(response.relief_report);
                },
                error: function (xhr, status, error) {
                    console.error(error);
                }
            });
        }

        function displayRelief(reliefData) {
            var tableBody = $('#reliefTable tbody');
            tableBody.empty(); // Clear existing data

            // Initialize counters for each status
            var totalConfirmed = 0;
            var totalPending = 0;
            var totalRejected = 0;

            // Iterate through reliefData and append rows
            reliefData.forEach(function (relief, index) {
                var row = $('<tr></tr>');
                row.append('<td>' + (index + 1) + '</td>');
                row.append('<td>' + relief.class_name + '</td>');
                row.append('<td>' + relief.subject + '</td>');
                row.append('<td>' + relief.slot + '</td>');
                row.append('<td>' + relief.leave_teacher + '</td>');
                row.append('<td>' + relief.relief_teacher + '</td>');

                // Set color based on status
                var statusColor;
                switch (relief.confirmation) {
                    case 'Rejected':
                        statusColor = 'red';
                        totalRejected++; // Increment rejected count
                        break;
                    case 'Confirmed':
                        statusColor = 'green';
                        totalConfirmed++; // Increment confirmed count
                        break;
                    case 'Pending':
                        statusColor = 'orange'; // Change 'yellow' to 'orange'
                        totalPending++; // Increment pending count
                        break;
                    default:
                        statusColor = 'black'; // Default color for unknown status
                        break;
                }

                // Append status column with the specified color
                row.append('<td style="color: ' + statusColor + ';">' + relief.confirmation + '</td>');

                tableBody.append(row);
            });

            // Update the total blocks with the counts
            $('.total_confirmed').text('Total Confirmed: ' + totalConfirmed);
            $('.total_pending').text('Total Pending: ' + totalPending);
            $('.total_rejected').text('Total Rejected: ' + totalRejected);
        }

        function autoSuggest(){
        var organization = $("#organization option:selected").val();
        var pendingRelief = ['1-1','2-2']; //get from each row and format it, 'leave_relief_id-schedule_subject_id'
        var criteria = 'class_in_week'; //drop down select
       
        $.ajax({
                url: "{{route('schedule.autoSuggestRelief')}}",
                type: 'POST',
                data: {
                    organization: organization, 
                    pendingRelief: pendingRelief, 
                    criteria: criteria
                },
                success: function(response) {
                    console.log(response); //update the combo box that select the teacher
                },
                error: function(xhr, status, error) {
                    console.log(error);
                }
            });
        }

        function dateOnChange() {
        let date_val = $('#datepicker').val(), timePicker = $('#timepicker'), timeRange = $('.time-range')
        let org_id = $('#organization option:selected').val()
        // console.log(date_val)
        if(date_val != '') {
            $('.pickup-time-div').removeAttr('hidden')
        } else {
            $('.pickup-time-div').attr('hidden', true)
        }
        }

    var disabledDates = dates
    
    function editDays(date) {
      for (var i = 0; i < disabledDates.length; i++) {
        if (new Date(disabledDates[i]).toString() == date.toString()) {             
          return [false];
        }
      }
      return [true];
    }
</script>
@endsection