@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/css/required-asterick.css')}}" rel="stylesheet">
{{-- <link href="{{ URL::asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet"> --}}
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<link rel="stylesheet" href="{{ URL::asset('assets/css/datatable.css')}}">

<style>

u:hover {
    color:red;
}
.tags {
  display: inline;
  position: relative;
}

.tags:hover:after {
  background: #333;
  background: rgba(0, 0, 0, .8);
  border-radius: 5px;
  top: 34px;
  color: #fff;
  content: attr(data-title);
  left: 0; /* Adjusted to start from the most left position */
  padding: 5px 15px;
  position: absolute;
  z-index: 98;
  width: min(60vw, 450px);
}

.tags:hover:before {
  border: solid;
  border-color: transparent transparent #333;
  border-width: 6px 6px 0 6px;
  top: 24px;
  content: "";
  left: 50%; /* Centered horizontally */
  transform: translateX(-50%);
  position: absolute;
  z-index: 99;
}

@media screen and (max-width: 500px) {
    .tags:hover:after {
    width: 60vw; /* Set the width to be 100% of the viewport width */
    left: 50%; /* Start from the center */
    transform: translateX(-50%); /* Offset by 50% of the element's width to the left */
    box-sizing: border-box; /* Include padding and border in the element's size */
    padding: 5px 15px; /* Add some padding inside the tooltip */
    white-space: normal; /* Allow the text to wrap as needed */
    text-align: center; /* Center-align the text */
  }
  .tags:hover:before {
    left: 50%; /* Keep the arrow centered */
    transform: translateX(-50%); /* Adjust translateX for precise centering */
  }
  .tags::-webkit-scrollbar{
    width:0;
}

 
}
</style>
@include('layouts.datatable')
@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Relief Management</h4>
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
                </div>

                <div class="form-group">
                    <label>Date</label>
                    <input type="text" value="" class="form-control" name="pickup_date" id="datepicker"  placeholder="Pilih tarikh" readonly required>
                </div>

                <div class="form-group">
                    <label>Auto Suggestion Sort By:</label>
                    <select name="sort" id="sort" class="form-control">
                        <!-- <option value="0" selected disabled>Sort By</option> -->
                        <option value="Beban Guru">Busy Slot</option>
                        <option value="Kelas">Class</option>
                        <option value="Subjek">Subject</option>
                    </select>
                </div>

               

                <!-- <a style="margin: 19px; float: right;" onclick="autoSuggest()" class="btn btn-primary"> <i class="fas fa-plus"></i> Auto Suggestion</a> -->
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
                <p style="font-size: 18px;">Ex. What is <u class ="tags" data-title="5S means 5 Busy Slot, this value calculated by the normal class add relief class taken">5S</u>
                <u  class ="tags" data-title="2R means 2 Remaining Relief Slot, this value calculated by the teacher maximum relief slot minus relief class taken">2R?</u>
                </p>
               
                <div class="table-responsive">
                    <table id="reliefTable" class="table table-striped dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr style="text-align:center">
                                <th>No </th>
                                <th>Class</th>
                                <th>Subject</th>
                                <th>Slot</th>
                                <th>Orginal Teacher</th>
                                <th>Reason</th>
                                <th data-orderable="false">Substitute Teacher</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>

                <div class="row">
                    <a style="margin: 19px;" href="#" class="btn btn-primary ml-auto" data-toggle="modal" data-target="#modelId">
                        <i class="fas fa-plus"></i> Add Row
                    </a>
                </div>

               
                <div class="row">
                    <!-- <a style="margin: 10px;" class="btn btn-danger">
                        <i class="fas fa-plus"></i> Discard
                    </a> -->

                    <form action="{{route('schedule.saveRelief')}}" method="post" id="commitReliefForm">
                        @csrf 
                        <input type="hidden" name="commitRelief" id="commitReliefInput">
                        <input type="hidden" name="organization" id="commitReliefOrg">
                        <a style="margin: 10px;" class="btn btn-success" onclick="saveRelief()">
                            <i class="fas fa-plus"></i> Confirm and Notify Related Teachers
                        </a>
                    </form>
                   
                </div>

            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Row</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{route('schedule.addTeacherLeave')}}" method="post" enctype="multipart/form-data">
                        <div class="modal-body">

                            {{ csrf_field() }}

                            
                            <div class="form-group">

                            <div class="form-group">
                            <label>Teacher Name</label>
                            <select name="selectedTeacher" id="selectedTeacher" class="form-control">
                              
                            </select>
                            </div>
                            <div class="form-group">
                                <label>Time of Leave</label>
                                <input type="radio" name="isLeaveFullDay" id="fullday" onclick="displaySelectTimeFull()" checked> Full Day
                                <input type="radio" name="isLeaveFullDay2" id="period" onclick="displaySelectTime()"> Period
                            </div>
                            
                            <div id="selectTime" style="display: none;">
                                <div class="form-group">
                                    <label>Start Time</label>
                                    <input type="time" name="starttime" id="starttime" class="form-control">
                                </div>
                                <div class="form-group" >
                                    <label>End Time</label>
                                    <input type="time" name="endtime" id="endtime" class="form-control" onchange="return validateTimeEnd()">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="reason">Reason</label>
                                <select name="reason" id="reason" class="form-control">
                                
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="reason">Upload Image</label>
                                <input type="file" name="image" id="image">
                            </div>

                            <input type="hidden" name="date" value="" id="rowdate">
                            <div class="form-group">
                                <label for="note">Note</label>
                                <input type="text" name="note" id="note" placeholder="Enter your note here..." class="form-control">
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary" onclick="return validateTimeEnd()">Add</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div> 

    </div>
</div>

<div id="imageModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width: 40%;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="imageModalLabel">View Image</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <img id="modalImage" src="" alt="Modal Image" style="width: 80%; height: 70%; display: block; margin-left: auto; margin-right: auto;">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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



<script>   
    var dates = []
    var teachers = "";
    var reliefTable;

    $(document).ready(function() {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $("#datepicker").datepicker("setDate", new Date());
        dateOnChange();
        

        $('#organization').change(function() {
            var organizationid = $("#organization option:selected").val();

            $.ajax({
                url: "{{ route('schedule.getTeacherOfOrg') }}",
                type: 'POST',
                data: {
                    organization: $('#organization option:selected').val(),
                },
                success: function (response) {
                    response.teachers.forEach(function(teacher){
                        $('#selectedTeacher').append('<option value="' + teacher.teacher_id + '">' + teacher.name + '</option>');
                    });

                    response.leaveType.forEach(function(type){
                        $('#reason').append('<option value="' + type.id + '">' + type.type + '</option>');
                    });
                }
            });


            // console.log(organizationid);
            // fetch_data(organizationid);
        });

        if ($("#organization").val() != "") {
            $("#organization").prop("selectedIndex", 1).trigger('change');
            // fetch_data($("#organization").val());
        }

        $('#sort').on('change', function () {
            autoSuggest();
        });

        // csrf token for ajax
       

        $('.alert').delay(3000).fadeOut();

        $('#datepicker').change(function() {
            dateOnChange();
            $('#reliefTable').DataTable().destroy();

           fetchReliefData();
        })
        
        

        // Function to display relief data in the table
       
        // var table = $('#reliefTable');
        // console.log(table);

        // Initial fetch when the page loads
        fetchReliefData();

        });

        $("#datepicker").datepicker({
            minDate: 0,
            maxDate: '+1m',
            dateFormat: 'yy-mm-dd',
            dayNamesMin: ['Ahd', 'Isn', 'Sel', 'Rab', 'Kha', 'Jum', 'Sab'],
            beforeShowDay: editDays,
            defaultDate: 0, 
        })

        function validateTimeEnd() {
            var startTime = document.getElementById('starttime').value;
            var endTime = document.getElementById('endtime').value;
            console.log(endTime);
            // Convert time strings to Date objects for easier comparison
            var startDate = new Date('1970-01-01T' + startTime + 'Z');
            var endDate = new Date('1970-01-01T' + endTime + 'Z');

            // Compare start and end times
            if (endDate <= startDate) {
                alert('End time cannot be earlier than or equal to start time.');
                // endTime = '';
                document.getElementById('endtime').value = '';
                console.log(endTime);
                return false;
            }

            return true;
        }

        function assignTeacher(leave_relief_id) {
           // console.log('Selected teacher for row ' + (schedule_subject_id) + ': ' + teacher);

            // Make an AJAX request to get available teachers for the selected slot
            $.ajax({
                url: "{{ route('schedule.getFreeTeacher') }}",
                type: 'POST',
                data: {
                    organization: $('#organization option:selected').val(),
                    date: $('#datepicker').val(),
                    leave_relief_id: leave_relief_id
                },
                success: function (response) {
                    console.log(response);

                    // Update the combo box that selects the teacher
                    updateTeacherComboBox(leave_relief_id, response.free_teacher_list);
                    
                    // console.log(response.free_teacher_list);
                    // Additional logic if needed
                },
                error: function (xhr, status, error) {
                    console.log(error);
                }
            });

            // Add your logic to handle the selected teacher here
            // You can make an AJAX request to update the server or perform other actions
        }


        function saveRelief(){
            var tableBody = $('#reliefTable tbody');
            var assignTeacherElements = tableBody.find('.assign_teacher');

            let commitRelief = []; 
            assignTeacherElements.each(function(index, element) {
                        var leave_relief = $(this).attr('data-index');
                        // Use direct property access instead of split
                        var teacher = $(this).val();
                        if(teacher == 0 || teacher ==null || teacher == 'No Teacher') 
                            return true;
                        commitRelief.push(leave_relief+'-'+teacher);
                    });

            if(commitRelief.length ==0)
            {
                alert('No update be make');
                return;
            }
            console.log(commitRelief);
            let commitReliefOrg = $('#organization option:selected').val();
            $('#commitReliefInput').val(JSON.stringify(commitRelief));
            $('#commitReliefOrg').val(commitReliefOrg);
            $('#commitReliefForm').submit();
        }

    function imageLinkFunction(){
        $(this).click(function() {
        var imageUrl = $(this).data("image");
        $("#modalImage").attr("src", "{{ URL::asset('/schedule_leave_image/') }}" + "/" + imageUrl);
        $("#imageModal").modal("show");
        return false;
    });
    }


// Call fetchReliefData with the corrected success function
function fetchReliefData() {
    let date_val = $('#datepicker').val();

    if (!date_val) {
        date_val = $.datepicker.formatDate('yy-mm-dd', new Date());
        $('#datepicker').datepicker('setDate', date_val);
    }

    $.ajax({
        url: '{{ route("schedule.getAllTeacher") }}',
        type: 'POST',
        data: {
            organization: $('#organization option:selected').val(),
            date: date_val,
        },
        success: function (response) {
            teachers= response.teachers;
            //displayRelief(response.pending_relief);
            // initializeDataTable();
        },
        error: function (xhr, status, error) {
            console.error(error);
        }
    });
    //$('#reliefTable').DataTable().destroy();
    teacherTable = $('#reliefTable').DataTable({
                processing: true,
                //serverSide: true,
                ajax: {
                    url: "{{ route('schedule.datatablePendingRelief') }}",
                    data: {
                        organization: $('#organization option:selected').val(),
                        date: date_val,
                    },
                    type: 'POST',

                },
                'columnDefs': [{
                    "targets": [0], // your case first column
                    "className": "text-center",
                    "width": "2%"
                }, {
                    "targets": [3, 4, 5, 6], // your case first column
                    "className": "text-center",
                }, ],
                columns: [{
                    "data": null,
                    searchable: false,
                    "sortable": false,
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                }, {
                    data: "class_name",
                    name: 'class_name'
                }, {
                    data: "subject",
                    name: 'subject'
                }, {
                    data: "slot",
                    name: 'slot'
                }, {
                    data: "leave_teacher",
                    name: 'leave_teacher'
                }, {
                    data: 'reason',
                    name: 'reason',
                }, {
                    data: 'combobox',
                    name: 'combobox',
                    orderable: false,
                    searchable: false
                }, ]
            });

    teacherTable.off('draw.dt').on('draw.dt', function () {
        $('.assign_teacher').change(function() {
            var selectedValue = $(this).val();
            var slot = $(this).attr('slot');
            //$(this).css("color", $("select option:selected").css("color"));
            var selectedOption = $(this).find('option:selected');

            // Check the color of the selected option
            var optionColor = selectedOption.css('color');

            // Update the color of the select element
            $(this).css('color', optionColor);

            // Use filter to find elements with the same slot and teacher
            if(selectedOption.text().trim() == 'No Teacher' || $(this).val() == null){
                return;
            }
            var duplicates = $('.assign_teacher').filter(function() {
                return $(this).val() === selectedValue && $(this).attr('slot') === slot ;
            });
            // Check if duplicates were found
            if (duplicates.length > 1) {
                console.log(selectedOption.text(),$(this).val(),duplicates)
                alert(selectedOption.text() +' was selected in the same slot!');
            }

            var checkRelief = $('.assign_teacher').filter(function() {
                return $(this).val() === selectedValue && $(this).attr('slot') !== slot;
            });

            if(checkRelief.length >= parseInt( selectedOption.attr('remaining_relief')) ){
                alert('This teacher exceed his/her remaining relief already!');
            }
        });

        var tableBody = $('#reliefTable').find('tbody');
        var assignTeacherElements = tableBody.find('.assign_teacher');
        console.log(assignTeacherElements);
        assignTeacherElements.each(function (index, element) {
            var selectedIndex = $(this).attr('data-index');
            // Use direct property access instead of split
            var leave_relief_id = selectedIndex;

            assignTeacher(leave_relief_id);

            // Your code to handle each element goes here console.log($(element).text()); // Example: Log the text content of each element using jQuery
        });
        autoSuggest();
    });
   
}


function updateTeacherComboBox(leave_relief_id,availableTeachers) {

    // Find the select box associated with the given schedule_subject_id
    var selectBox = $('.assign_teacher[data-index="' + leave_relief_id + '"]');

    // Clear existing options
    selectBox.empty();

    //Check if availableTeachers is defined
    selectBox.append('<option selected style="color:DarkOrange;" >No Teacher</option>');
    if (availableTeachers) {
        // If availableTeachers is an object, extract the array
        var teachersArray = Array.isArray(availableTeachers) ? availableTeachers : availableTeachers.free_teacher_list;
        const t = JSON.parse(teachers);
        //console.log(t);
        // Check if the extracted array is not empty
        if (Array.isArray(teachersArray) && teachersArray.length > 0) {
            // Iterate over the array and add options
            teachersArray.forEach(function (teacher) {
                var foundTeacher = t.find(function (tid) {
                    return teacher.id === tid.id;
                });
                if(foundTeacher){
                    //console.log(foundTeacher)
                    var option = $('<option></option>').attr('value', foundTeacher.id).text(foundTeacher.name + '('+ foundTeacher.details.busySlot + 'S' + foundTeacher.details.remaining_relief + 'R)');
                    option.attr('remaining_relief',foundTeacher.details.remaining_relief )
                    // Check if the current teacher is the found teacher
                   // console.log(!(foundTeacher.details.remaining_relief >0),foundTeacher);
                    if (foundTeacher.details.remaining_relief <=0) {
                        option.css('color', 'red'); // Set text color to red
                        //*console.log('me');
                    }else{
                        option.css('color', 'black');
                    }

                    selectBox.append(option);
                }
                
            });
        }
    }

    selectBox.trigger('change');
    
}

    function autoSuggest(){
        var organization = $("#organization option:selected").val();
        var tableBody = $('#reliefTable tbody');
        var assignTeacherCombobox = tableBody.find('.assign_teacher');
        let pendingRelief = []; 
        assignTeacherCombobox.each(function (index, relief){
        // There's a typo in 'schedule_subvject_id'. It should be 'schedule_subject_id'
            var ss_id = $(relief).attr('schedule_subject_id');
            var lr_id = $(relief).attr('data-index');

            // There's a typo in 'ss.id'. It should be 'ss_id'
            pendingRelief.push(lr_id+'-'+ss_id);
        });
        console.log(pendingRelief)//get from each row and format it, 'leave_relief_id-schedule_subject_id'
        var criteria =  $('#sort').val();
       
        $.ajax({
                url: "{{route('schedule.autoSuggestRelief')}}",
                type: 'POST',
                data: {
                    organization: organization, 
                    pendingRelief: pendingRelief, 
                    teachers:teachers,
                    criteria: criteria,
                    date: $('#datepicker').val(),
                },
                success: function(response) {
                    console.log(response);
                    $(".assign_teacher").prop("selectedIndex", 0).trigger('change');
                     response.relief_draft.forEach(function(draft,index){
                        //var combobox = assignTeacherCombobox.has("[data-index='"+draft.leave_relief_id+"']");
                        var combobox = $('.assign_teacher[data-index="'+draft.leave_relief_id+'"]');
                        //combobox.val('17478');
                        combobox.val(draft.teacher_id)
                        combobox.trigger('change');
                        console.log(draft.teacher_id)
                     })
                },
                error: function(xhr, status, error) {
                    console.log(error);
                }
            });
        }

        function dateOnChange() {
            let date_val = $('#datepicker').val(), timePicker = $('#timepicker'), timeRange = $('.time-range')
            let org_id = $('#organization option:selected').val()
            
             console.log(date_val)
            $('#rowdate').val(date_val);
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

    function displaySelectTime() {
        var selectTimeDiv = document.getElementById('selectTime');
        var periodRadio = document.getElementById('period');
        var fulldayRadio = document.getElementById('fullday');
        
        if (periodRadio.checked) {
            selectTimeDiv.style.display = 'block';
            fulldayRadio.checked = false;

            // Set the 'required' attribute for the starttime and endtime fields
            document.getElementById('starttime').setAttribute('required', 'required');
            document.getElementById('endtime').setAttribute('required', 'required');
        } else {
            selectTimeDiv.style.display = 'none';

            // Remove the 'required' attribute for the starttime and endtime fields
            document.getElementById('starttime').removeAttribute('required');
            document.getElementById('endtime').removeAttribute('required');
        }
    }

    function displaySelectTimeFull() {
        var selectTimeDiv = document.getElementById('selectTime');
        var periodRadio = document.getElementById('period');
        var fulldayRadio = document.getElementById('fullday');

        if (fulldayRadio.checked) {
            selectTimeDiv.style.display = 'none';
            periodRadio.checked = false;

            // Remove the 'required' attribute for the starttime and endtime fields
            document.getElementById('starttime').removeAttribute('required');
            document.getElementById('endtime').removeAttribute('required');
        } else {
            selectTimeDiv.style.display = 'block';

            // Set the 'required' attribute for the starttime and endtime fields
            document.getElementById('starttime').setAttribute('required', 'required');
            document.getElementById('endtime').setAttribute('required', 'required');
        }
    }

    
    //     function displayRelief(reliefData) {
    //     console.log('Relief Data:', reliefData);

    //             var tableBody = $('#reliefTable tbody');
    //             tableBody.empty();
                
    //             reliefData.forEach(function (relief, index) {
    //                 var row = $('<tr></tr>');
    //                 row.append('<td>' + (index + 1) + '</td>');
    //                 row.append('<td>' + relief.class_name + '</td>');
    //                 row.append('<td>' + relief.subject + '</td>');
    //                 row.append('<td>' + relief.slot + '</td>');
    //                 row.append('<td>' + relief.leave_teacher + '</td>');
                    
    //                 var tdElement = $('<td></td>');
    //                 if (relief.desc !== null) {
    //                      tdElement = $('<td>' + relief.desc + '</td>');
    //                 }

    //                 if (relief.image !== null) {
    //                     // Add a link to open the modal and display the image
    //                     var imageLink = $('<a href="#" class="image-link" data-image="' + relief.image + '">(View)</a>');
    //                     imageLink.click(function() {
    //                             // Get the image URL from the data attribute
    //                             var imageUrl = $(this).data('image');
    //                             $('#modalImage').attr('src', '{{ URL::asset('/schedule_leave_image/') }}' + '/' + imageUrl);

    //                             $('#imageModal').modal('show');

    //                             return false;
    //                             console.log('Image URL:', imageUrl);
    //                         });
    //                     tdElement.append(imageLink);
    //                 }

    //                 row.append(tdElement);

    //     // Append the select box with options
    //         var selectColumn = $('<td><select class="form-control assign_teacher" data-index="' + relief.leave_relief_id  
    //         + '" schedule_subject_id="'+relief.schedule_subject_id +'" slot = "'+relief.slot+'"></select></td>');

    //         var selectElement = selectColumn.find('select');

    //         // Call the updated function to populate the select box options
    //     //git  updateTeacherComboBox(index, response.original.free_teacher_list);

    //         row.append(selectColumn);
    //         tableBody.append(row);
    //     });
        
    //         var assignTeacherElements = tableBody.find('.assign_teacher');

    // $('.assign_teacher').change(function() {
    //     var selectedValue = $(this).val();
    //     var slot = $(this).attr('slot');
    //     //$(this).css("color", $("select option:selected").css("color"));
    //     var selectedOption = $(this).find('option:selected');

    //     // Check the color of the selected option
    //     var optionColor = selectedOption.css('color');

    //     // Update the color of the select element
    //     $(this).css('color', optionColor);

    //     // Use filter to find elements with the same slot and teacher
    //     if(selectedOption.text().trim() == 'No Teacher' || $(this).val() == null){
    //         return;
    //     }
    //     var duplicates = $('.assign_teacher').filter(function() {
    //         return $(this).val() === selectedValue && $(this).attr('slot') === slot ;
    //     });
    //     // Check if duplicates were found
    //     if (duplicates.length > 1) {
    //         console.log(selectedOption.text(),$(this).val(),duplicates)
    //         alert(selectedOption.text() +' was selected in the same slot!');
    //     }

    //     var checkRelief = $('.assign_teacher').filter(function() {
    //         return $(this).val() === selectedValue && $(this).attr('slot') !== slot;
    //     });
      
    //     if(checkRelief.length >= parseInt( selectedOption.attr('remaining_relief')) ){
    //         alert('This teacher exceed his/her remaining relief already!');
    //     }
        
    // });

    
    //     assignTeacherElements.each(function(index, element) {
    //                 var selectedIndex = $(this).attr('data-index');
    //                 // Use direct property access instead of split
    //                 var leave_relief_id = selectedIndex;

    //                 assignTeacher(leave_relief_id);
            
    //         // Your code to handle each element goes here console.log($(element).text()); // Example: Log the text content of each element using jQuery
    //     });
    //     autoSuggest();

    //     }

</script>
@endsection

