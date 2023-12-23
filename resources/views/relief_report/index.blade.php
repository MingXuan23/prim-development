@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/css/required-asterick.css')}}" rel="stylesheet">
{{-- <link href="{{ URL::asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet"> --}}
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<link rel="stylesheet" href="{{ URL::asset('assets/css/datatable.css')}}">
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

                <!-- <div class="form-group">
                    <label>Tarikh</label>
                    <input type="text" value="" class="form-control" name="pickup_date" id="datepicker"  placeholder="Pilih tarikh" readonly required>
                </div> -->

                <div class="form-group">
                        <label>Start Time:</label>
                        <input type="text" value="" class="form-control" name="pickup_date" id="datepicker_start"  placeholder="Pilih tarikh" readonly required>
                        <label>End Time:</label>
                        <input type="text" value="" class="form-control" name="pickup_date" id="datepicker_end"  placeholder="Pilih tarikh" readonly required>
                    </div>

                <div>
                    <label>Report Type: </label>
                    <div>
                    <button type="button" id="chart" name="chart" class="btn btn-primary">Daily Status (Default)</button>
                    <button type="button" id="lr_teacher" name="lr_teacher" class="btn btn-primary">Leave and Relief Teacher</button>
                    </div>
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

                <div id="choose-date">
                <button type="button" id="details" name="details" class="btn btn-primary">Show Details</button>
            <div id="chart-section">
                <div class="total_report">
                    <!-- <div class="total_confirmed"></div>
                    <div class="total_pending"></div>
                    <div class="total_rejected"></div> -->
                    
                    <canvas id="barChart" width="300" height="100"></canvas>
                </div>
            </div>

            <div id="details-section">
                <div class="table-responsive">
                    <table id="reliefTable" class="table table-bordered table-striped dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr style="text-align:center">
                                <th>No </th>
                                <th>Tarikh</th>
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
            </div>

                </div>
            
                <div id="lr-teacher-section" style="display: none;">
                    <div class="form-group">
                        <label>Select Teacher:</label>
                        <input type="text" name="select_teacher" id="select_teacher" class="form-control">
                    </div>
                    <div class="table-responsive">
                    <table id="teacherTable" class="table table-bordered table-striped dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr style="text-align:center">
                                <th>No </th>
                                <th>Nama Guru</th>
                                <th>Slot ganti yg telah ambil</th>
                                <th>Slot ganti yg tinggal</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                    <div class="total_report">
                        <canvas id="lrTeacherChart" width="300" height="100"></canvas>
                    </div>
                </div>

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
        $("#datepicker_start").datepicker("setDate", new Date());
        $("#datepicker_end").datepicker("setDate", new Date());
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

        $('#datepicker_start').change(function() {
        //    dateOnChange();
        fetchReliefData($('#datepicker_start').val(), $('#datepicker_end').val());
        console.log($('#datepicker_start').val(), $('#datepicker_end').val());

        })

        // Initial fetch when the page loads
        fetchReliefData($('#datepicker_start').val(), $('#datepicker_end').val());
        console.log($('#datepicker_start').val(), $('#datepicker_end').val());
        });

        $("#datepicker_start").datepicker({
            minDate: '-1m',
            maxDate: '+1m',
            dateFormat: 'yy-mm-dd',
            dayNamesMin: ['Ahd', 'Isn', 'Sel', 'Rab', 'Kha', 'Jum', 'Sab'],
            beforeShowDay: editDays,
            defaultDate: 0, 
        });

        $("#datepicker_end").datepicker({
            dateFormat: 'yy-mm-dd',
            dayNamesMin: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
            onSelect: function(selectedDate) {
                // Trigger the change event when a date is selected
                $('#datepicker_end').trigger('change');
            }
        });

        // function fetchReliefData() {
        //     let date_val = $('#datepicker_start').val();
        //     // If date is empty, set it to today
        //     if (!date_val) {
        //         date_val = $.datepicker.formatDate('yy-mm-dd', new Date());
        //         $('#datepicker_start').datepicker('setDate', date_val); // Update datepicker value
        //     }
        //     console.log(date_val);
        //     $.ajax({
        //         url: '{{ route("schedule.getReliefReport") }}',
        //         type: 'POST',
        //         data: {
        //             organization: $('#organization option:selected').val(), 
        //             date: date_val,
        //             // Replace with your organization ID
        //         },
        //         success: function (response) {
        //             console.log(response); // Log the pending relief data
        //             //console.log(response.available_teachers); // Log the available teachers data 
        //             displayRelief(response.relief_report);
        //         },
        //         error: function (xhr, status, error) {
        //             console.error(error);
        //         }
        //     });
        // }

        $('#datepicker_end').change(function() {
            // Additional logic when datepicker_end changes
            // You can add any custom logic here
            fetchReliefData($('#datepicker_start').val(), $(this).val());
            console.log($('#datepicker_start').val(), $('#datepicker_end').val());
        });

        function fetchReliefData(start_date, end_date) {
            if (end_date === null) {
                // If date_end is null, set end_date to start_date
                end_date = start_date;
            }
        $.ajax({
            url: '{{ route("schedule.getReliefReport") }}',
            type: 'POST',
            data: {
                organization: $('#organization option:selected').val(),
                start_date: start_date,
                end_date: end_date,
            },
            success: function (response) {
                console.log(response); // Log the pending relief data
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
                row.append('<td>' + relief.date + '</td>');
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

                row.append('<td style="color: ' + statusColor + ';">' + relief.confirmation + '</td>');

                tableBody.append(row);
            });

            // Update the total blocks with the counts
            // $('.total_confirmed').text('Total Confirmed: ' + totalConfirmed);
            // $('.total_pending').text('Total Pending: ' + totalPending);
            // $('.total_rejected').text('Total Rejected: ' + totalRejected);

            // Update the bar chart
            updateBarChart(totalConfirmed, totalPending, totalRejected);
        }

        var barChart; // Declare the chart variable globally

        function updateBarChart(confirmed, pending, rejected) {
            var total = confirmed + pending + rejected;
            var ctx = document.getElementById('barChart').getContext('2d');

            // Destroy the existing chart if it exists
            if (barChart) {
                barChart.destroy();
            }

            barChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Confirmed', 'Pending', 'Rejected'],
                    datasets: [{
                        label: 'Total Report (' + total + ')',
                        data: [confirmed, pending, rejected],
                        backgroundColor: [
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(255, 99, 132, 0.2)',
                        ],
                        borderColor: [
                            'rgba(75, 192, 192, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(255, 99, 132, 1)',
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
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
        let date_val = $('#datepicker_start').val(), timePicker = $('#timepicker'), timeRange = $('.time-range')
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

    var displayMode = 'chart';

        // Function to switch between 'details' and 'lr_teacher' modes
        function switchDisplayMode(mode) {
            if (mode === 'details') {
                $('#chart-section').hide();
                $('#details-section').show();
                $('#lr-teacher-section').hide();
            } else if (mode === 'chart') {
                $('#chart-section').show();
                $('#details-section').hide();
                $('#lr-teacher-section').hide();
            } else if (mode === 'lr_teacher') {
                $('#choose-date').hide();
                $('#lr-teacher-section').show();
            }
        }

        // Initial setup
        switchDisplayMode(displayMode);

        $('#chart').click(function() {
            displayMode = 'chart';
            switchDisplayMode(displayMode);
        });

        // Event handler for 'details' button
        $('#details').click(function() {
            // Toggle the text of the button based on the current mode
            if (displayMode === 'details') {
                displayMode = 'chart';
                $(this).text('Show Details');
            } else {
                displayMode = 'details';
                $(this).text('Show Chart');
            }

            switchDisplayMode(displayMode);
        });

        // Event handler for 'lr_teacher' button
        $('#lr_teacher').click(function() {
            displayMode = 'lr_teacher';
            switchDisplayMode(displayMode);
            displayTeacher();
        });
        
        function fetchTeacher(){
            
        }

        function displayTeacher() {

        }

</script>
@endsection