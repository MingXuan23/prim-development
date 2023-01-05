@extends('layouts.master')

@section('css')

@endsection

@section('content')

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
            <table class="table table-striped table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th style="width: 10%" class="text-center">Hari</th>
                        <th style="width: 10%" class="text-center">Buka/Tutup</th>
                        <th style="width: 20%">Waktu Buka</th>
                        <th style="width: 20%">Waktu Tutup</th>
                        <th style="width: 10%" class="text-center">Action</th>
                    </tr>
                </thead>
                
                <tbody>
                    @foreach($hour as $row)
                    <tr>
                        <td class="text-center align-middle">{{ $day_name[$row->day] }}</td>
                        <td class="text-center align-middle">
                            {!! ($row->status == 1) 
                            ? "<span class='badge rounded-pill bg-success text-white'>Buka</span>"
                            : "<span class='badge rounded-pill bg-danger text-white'>Tutup</span>" !!}
                        </td>
                        <td class="align-middle">
                            {!! ($row->status == 1) 
                            ? date_format(date_create($row->open_hour), "h:i A")
                            : "" !!}    
                        </td>
                        <td class="align-middle">
                            {!! ($row->status == 1) 
                            ? date_format(date_create($row->close_hour), "h:i A")
                            : "" !!}
                        </td>
                        <td class="text-center align-middle">
                            <button data-hour-id="{{ $row->id }}" class="edit-time-btn btn btn-primary"><i class="fas fa-pencil-alt"></i></button>
                        </td>
                    </tr>
                    @endforeach
                    <tr>
                        <td colspan="5" class="text-center"><i>Sebarang perubahan boleh menyebabkan pesanan perlu dikemaskini.</i></td>
                    </tr>
                </tbody>
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

        var hour_id, status, arr_order_id

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('.edit-time-btn').click(function() {
            hour_id = $(this).attr('data-hour-id')

            $.ajax({
                url: "{{ route('admin-reg.edit-hour') }}",
                method: "POST",
                data: {hour_id:hour_id},
                beforeSend:function() {
                    $('#editOperationHourModal').modal('show')
                    $('#open_hour').val("")
                    $('#close_hour').val("")
                    $('.status option').removeAttr('selected')
                    $('.status').prop('disabled', false)
                    $('#open_hour').prop('disabled', true)
                    $('#close_hour').prop('disabled', true)
                    $('.alert-danger').hide()
                    $('#btn-update-hour').prop('disabled', false)
                    $('.order-exists').empty()
                },
                success:function(result) {
                    $('.day-name').empty().append(result.day_name[result.hour.day])
                    if(result.hour.status == 1)
                    {
                        $('.status option[value='+result.hour.status+']').attr('selected', true)
                        $('#open_hour').prop('disabled', false)
                        $('#close_hour').prop('disabled', false)
                        $('#open_hour').val(result.hour.open_hour)
                        $('#close_hour').val(result.hour.close_hour)
                    }
                    else
                    {
                        $('.status').removeAttr('selected').filter('[value='+result.hour.status+']').attr('selected', true)
                    }
                },
                error:function(result) {
                    console.log(result.responseText)
                }
            })
            
        })

        $('.status').change(function() {
            status = $(this).children(':selected').val()
            if(status == 1) {
                $('#open_hour').prop('disabled', false)
                $('#open_hour').val("")
                $('#close_hour').val("")
            }
            else {
                $('#open_hour').prop('disabled', true)
                $('#close_hour').prop('disabled', true)
                $('#open_hour').val("")
                $('#close_hour').val("")
            }
        })

        $('#open_hour').change(function () {
            if($(this).val() != "") {
                $('#close_hour').prop('disabled', false)
            } else {
                $('#close_hour').prop('disabled', true)
            }
        })

        $('#btn-update-hour').click(function() {
            var status = $('.status').children(':selected').val()
            var open_hour = $('#open_hour').val()
            var close_hour = $('#close_hour').val()

            $.ajax({
                url: "",
                method: "PUT",
                data: {hour_id:hour_id, status:status, open_hour:open_hour, close_hour:close_hour},
                beforeSend:function() {
                    $('.alert-danger').empty()
                },
                success:function(result) {
                    if(result.status == "error_order") {
                        $('.alert-danger').show().append(result.alert)
                        $('#btn-check-order').show()
                        $('#btn-update-hour').prop('disabled', true)
                        $('.order-exists').append(result.order)
                        $('.status').prop('disabled', true)
                        $('#open_hour').prop('disabled', true)
                        $('#close_hour').prop('disabled', true)
                        console.log(result)
                    }
                    else if(result.status == "error_time") {
                        $('.alert-danger').show().append(result.alert)
                    }
                    else if(result.status == "success") {
                        location.reload()
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