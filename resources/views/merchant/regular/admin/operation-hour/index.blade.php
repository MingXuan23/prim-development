@extends('layouts.master')

@section('css')
<link rel="stylesheet" href="{{ URL::asset('assets/css/datatable.css')}}">
@include('layouts.datatable')
<style>
.active{
    background-color: #5b626b!important;
    border: 2px solid #5b626b!important;
    color:#ffffff!important;
}
.inactive{
    background-color: #af1717!important;
    border: 2px solid #af1717!important;
    color:#ffffff!important;
}
.btn-grey{
    background-color: #5b626b!important;
    border: 2px solid #5b626b!important;
    color:#ffffff!important;
}
.btn-grey:hover{
    background-color: #ffffff!important;
    color:#5b626b!important;
    border: 2px solid #5b626b!important;
}
 </style>   
@endsection

@section('content')
<div style="padding-top: 12px" class="row">
    <div class="col-md-12 ">
        <div class=" align-items-center">
            <div class="form-group card-title">
                <select name="org" id="org_dropdown" class="form-control col-md-12">
                    <option value="">Pilih Organisasi</option>
                    @foreach($merchant as $row)
                    <option value="{{ $row->id }}">{{ $row->nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>

<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Waktu Operasi</h4>
        </div>
    </div>
</div>

@csrf

<div class="card">
    <div class="card-body">
        @if(Session::has('success'))
        <div class="alert alert-success" id="session">
            <p id="success">{{ Session::get('success') }}</p>
        </div>
        @endif
        <div class="table-responsive">
            <table class="table table-striped dt-responsive wrap" id="hourTable" width="100%" cellspacing="0">
            <thead class="thead-dark">
                <tr>
                    <th style="width: 10%" class="text-center">Hari</th>
                    <th style="width: 10%" class="text-center">Buka/Tutup</th>
                    <th style="width: 20%" class="text-center">Waktu Buka</th>
                    <th style="width: 20%" class="text-center">Waktu Tutup</th>
                    <th style="width: 10%" class="text-center">Action</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <td colspan="5" class="text-center"><i>Sebarang perubahan boleh menyebabkan pesanan perlu dikemaskini.</i></td>
                </tr>
            </tfoot>
            </table>
        </div>
    </div>
</div>

{{-- Edit Operation Hours Modal --}}
<div id="editOperationHourModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Kemaskini Hari <strong class="day-name"></strong></h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                @csrf
                <div class="alert alert-danger"></div>
                <div class="row mb-3">
                    <label class="col col-form-label pl-4">Status</label>
                    <div class="col-9 pr-4">
                        <select class="form-control status" required>
                            <option value="" disabled selected>Pilih Status Hari</option>
                            <option value="0" >Tutup</option>
                            <option value="1" >Buka</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-group required">
                            <label class="col">Waktu Buka</label>
                            <div class="col">
                                <input class="form-control" type="time" id="open_hour" disabled required>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group required">
                            <label class="col">Waktu Tutup</label>
                            <div class="col">
                                <input class="form-control" type="time" id="close_hour" disabled required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-light">Tutup</button>
                <button type="button" id="btn-update-hour" class="btn btn-primary">Kemaskini</button>
                <div class="order-exists"></div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')

<script>
    $(document).ready(function() {
        $('#session').delay(2000).fadeOut()

        let hour_id, orgId,
            openHour = $('#open_hour'),
            closeHour = $('#close_hour'),
            alertDanger = $('.alert-danger'), 
            status = $('.status'), 
            dayName = $('.day-name'),
            dropdownLength = $('#org_dropdown').children('option').length
        
        if(dropdownLength > 1) {
            $('#org_dropdown option')[1].selected = true
            orgId = $('#org_dropdown option')[1].value
            fetch_data(orgId)
        }

        $('#org_dropdown').change(function() {
            orgId = $("#org_dropdown option:selected").val()
            if(orgId != ''){
                $('#hourTable').DataTable().destroy()
                fetch_data(orgId)
            }else {
                $('#hourTable').DataTable().destroy()
                fetch_data()
            }
        })

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function fetch_data(orgId = '') {
            hourTable = $('#hourTable').DataTable({
                "searching": false,
                "bLengthChange": false,
                "bPaginate": false,
                "info": false,
                "orderable": false,
                "ordering": false,
                processing: true,
                serverSide: true,
                "language": {
                    "zeroRecords": "Sila Pilih Organisasi"
                },
                ajax: {
                    url: "{{ route('admin-reg.get-oh') }}",
                    data: {
                        id:orgId,
                    },
                    type: 'GET',
                },
                'columnDefs': [{
                    "targets": [0, 1, 2, 3, 4], // your case first column
                    "className": "align-middle text-center", 
                },
                { "responsivePriority": 1, "targets": 0 },
                { "responsivePriority": 2, "targets": 1 },
                { "responsivePriority": 3, "targets": 4 },
                ],
                columns: [{
                    data: "day",
                    name: 'day',
                    orderable: false,
                    searchable: false,
                }, {
                    data: "status",
                    name: 'status',
                    orderable: false,
                    searchable: false,
                }, {
                    data: "open_time",
                    name: 'open_time',
                    orderable: false,
                    searchable: false,
                }, {
                    data: "close_time",
                    name: 'close_time',
                    orderable: false,
                    searchable: false,
                },{
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                },]
            });
        }

        // $(document).on('click', '.btn-done-pickup', function(){
            
        $(document).on('click', '.edit-time-btn', function(){
            
            hour_id = $(this).attr('data-hour-id')

            $.ajax({
                url: "{{ route('admin-reg.edit-hour') }}",
                method: "POST",
                data: {hour_id:hour_id},
                beforeSend:function() {
                    dayName.empty()
                    $('.status option').removeAttr('selected')
                    status.prop('disabled', false)
                    openHour.val("").prop('disabled', true)
                    closeHour.val("").prop('disabled', true)
                    alertDanger.hide()
                    $('#btn-update-hour').prop('disabled', false)
                    $('.order-exists').empty()
                },
                success:function(result) {
                    dayName.append(result.day_name[result.hour.day])
                    if(result.hour.status == 1)
                    {
                        $('.status option[value='+result.hour.status+']').attr('selected', true)
                        openHour.prop('disabled', false).val(result.hour.open_hour)
                        closeHour.prop('disabled', false).val(result.hour.close_hour)
                    }
                    else
                    {
                        status.removeAttr('selected').filter('[value='+result.hour.status+']').attr('selected', true)
                    }
                    $('#editOperationHourModal').modal('show')
                },
                error:function(result) {
                    console.log(result.responseText)
                }
            })
            
        })

        status.change(function() {
            statusVal = $(this).children(':selected').val()
            if(statusVal == 1) {
                openHour.prop('disabled', false).val("")
                closeHour.val("")
            }
            else {
                openHour.prop('disabled', true).val("")
                closeHour.prop('disabled', true).val("")
            }
        })

        openHour.change(function () {
            if($(this).val() != "") {
                closeHour.prop('disabled', false)
            } else {
                closeHour.prop('disabled', true)
            }
        })

        $('#btn-update-hour').click(function() {
            let statusVal = status.children(':selected').val()
            let openHourVal = openHour.val()
            let closeHourVal = closeHour.val()

            $.ajax({
                url: "{{ route('admin-reg.update-hour') }}",
                method: "PUT",
                data: {hour_id:hour_id, status:statusVal, open_hour:openHourVal, close_hour:closeHourVal},
                beforeSend:function() {
                    alertDanger.empty()
                },
                success:function(result) {
                    if(result.response.status == "order-exist") {
                        alertDanger.show().append(result.response.alert)
                        $('#btn-check-order').show()
                        $('#btn-update-hour').prop('disabled', true)
                        $('.order-exists').append(result.response.order)
                        status.prop('disabled', true)
                        openHour.prop('disabled', true)
                        closeHour.prop('disabled', true)
                    }
                    else if(result.response.status == "invalid-time") {
                        alertDanger.show().append(result.response.alert)
                    }
                    else if(result.response == "success") {
                        $('#hourTable').DataTable().destroy()
                        fetch_data(orgId)
                        $('#editOperationHourModal').modal('hide')
                    }
                },
                error:function(result) {
                    console.log(result.responseText)
                }
            })
        })

    })
    
</script>

@endsection