@extends('layouts.master')

@section('css')

@include('layouts.datatable');
<style>
  .scroll-indicator {
  display: none; /* Hide the alert by default */
}

@media (max-width: 500px) {
  /* Show the alert on screens smaller than or equal to 768px (mobile) */
  .scroll-indicator {
    display: block;
  }
}
</style>
@endsection

@section('content')

@if(count($errors) > 0)
  <div class="alert alert-danger">
      <ul>
          @foreach($errors->all() as $error)
          <li>{{$error}}</li>
          @endforeach
      </ul>
  </div>
@endif

@if(Session::has('success'))
  <div class="alert alert-success">
    <p id="success">{{ \Session::get('success') }}</p>
  </div>
@endif

<div class="row align-items-center">
  <div class="col-sm-12">
  <div style="padding-top: 24px" class="row">
      <div class="col-md-12 ">
          <div class=" align-items-center">
              <div class="form-group card-title">
                  <select name="org" id="org_dropdown" class="form-control col-md-12">
                      <option value="" selected disabled>Pilih Organisasi</option>
                      @foreach($koperasiList as $row)
                      <option value="{{ $row->organization_id }}">{{ $row->nama }}</option>
                      @endforeach
                  </select>
              </div>
          </div>
      </div>
</div>
  </div>
</div>

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">

        <form method="POST" action="{{route('koperasi.storeOpening')}}" class="form-validation">
        <label>
          
          <input type="checkbox" name="checkboxEnablePickUpTime" id="selectDate">
          Benarkan Pilih Masa Pengambilan
        </label>

        <input type="text" name="noteReq" id="noteReq" class="form-control" placeholder="Masukkan Format Nota"  value=""></br>
          {{csrf_field()}}
          <p class="scroll-indicator">Scroll > >></p>
        <div class="table-responsive">
          <table id="openingTable" class="table table-bordered table-striped dt-responsive wrap"
          style="border-collapse: collapse; border-spacing: 0; width: 100%;">

            <thead>
              <tr style="text-align:center">
                <th style="width: 20%">Hari</th>
                <th style="width: 20%">Status Buka</th>
                <th style="width: 30%">Waktu Buka</th>
                <th style="width: 30%">Waktu Tutup</th>
              </tr>
            </thead>

            <tbody>
              
              <tr>

                <td>
                <div class="col">
                            <div class="form-group required">
                                <select name="day" id ="day" class="form-control"
                                    data-parsley-required-message="Sila pilih hari" required>
                                   <option value="1">
                                    Isnin
                                   </option>
                                   <option  value="2">
                                    Selasa
                                   </option>
                                   <option  value="3">
                                    Rabu
                                   </option>
                                   <option value="4">
                                    Khamis
                                   </option>
                                   <option value="5">
                                    Jumaat
                                   </option>
                                   <option value="6">
                                    Sabtu
                                   </option>
                                   <option  value="0">
                                    Ahad
                                   </option>
                                </select>
                            </div>
                </div>
                </td>
                <td>

                <select name="status" id = "select_status" class="form-control">
                  <option value ="1">
                    available
                  </option>
                  <option value ="2">
                    not available
                  </option>
                </select>

                </td>
                
                <td>
               
                <div class="col-sm-10">
                <input class="form-control" type="time" value="00:00:00" id="open_hour" name="open">
                </div></br>

                </td>

                <td>
                <div class="col-sm-10">
                <input class="form-control" type="time" value="00:00:00" id="close_hour" name="close">
                </div></br>
                </td>

              </tr>
            
            </tbody>

          </table>
          </div>
          <div class="form-group mb-0">
            <div class="text-right">

                <a type="button" href=""
                    class="btn btn-secondary waves-effect waves-light mr-1">
                    Kembali
                </a>
                <input type="hidden" name="koopId" class="koperasi_id">
                <button type="submit" class="btn btn-primary waves-effect waves-light mr-1" id="btnSubmit">
                    Simpan
                </button>
                
            </div>
          </div>

        </form>

      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">

        <table id="openingTable" class="table table-bordered table-striped dt-responsive wrap"
        style="border-collapse: collapse; border-spacing: 0; width: 100%;">

          <thead>
            <tr style="text-align:center">
              <th style="width: 20%">Hari</th>
              <th style="width: 20%">Status Buka</th>
              <th style="width: 30%">Waktu Buka</th>
              <th style="width: 30%">Waktu Tutup</th>
            </tr>
          </thead>

          <tbody id="hourTable">

          </tbody>

        </table>

      </div>
    </div>
  </div>
</div>


@endsection

@section('script')

<script>
  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });

  function loadHourTable(){
    orgId = $("#org_dropdown option:selected").val();
    $('.koperasi_id').val(orgId);
    $.ajax({
            type: 'GET',
            url: '{{ route("koperasi.openingChangeKoperasi") }}',
            data: {
                koopId:orgId
            },
            success:function(response){
              //console.log(response.hour)
              document.getElementById('selectDate').checked= response.hour[0].date_selection_enable;
              //console.log( $('#selectDate').val(),response.hour[0].date_selection_enable);
              $('#noteReq').val(response.hour[0].note_requirement);
              $('#hourTable').empty();
              response.hour.forEach(function(hour) {
                var row = document.createElement('tr');

                var dayCell = document.createElement('td');
                if (hour.day == 1) {
                  dayCell.textContent = 'Isnin';
                } else if (hour.day == 2) {
                  dayCell.textContent = 'Selasa';
                } else if (hour.day == 3) {
                  dayCell.textContent = 'Rabu';
                } else if (hour.day == 4) {
                  dayCell.textContent = 'Khamis';
                } else if (hour.day == 5) {
                  dayCell.textContent = 'Jumaat';
                } else if (hour.day == 6) {
                  dayCell.textContent = 'Sabtu';
                } else if (hour.day == 0) {
                  dayCell.textContent = 'Ahad';
                }
                row.appendChild(dayCell);

                var statusCell = document.createElement('td');
                var statusBadge = document.createElement('div');
                statusBadge.classList.add('d-flex', 'justify-content-center');
                if (hour.status == 2) {
                  statusBadge.innerHTML = '<span class="badge badge-danger">tutup</span>';
                } else if (hour.status == 1) {
                  statusBadge.innerHTML = '<span class="badge badge-success">buka</span>';
                }
                statusCell.appendChild(statusBadge);
                row.appendChild(statusCell);

                var openHourCell = document.createElement('td');
                openHourCell.textContent = hour.open_hour;
                row.appendChild(openHourCell);

                var closeHourCell = document.createElement('td');
                closeHourCell.textContent = hour.close_hour;
                row.appendChild(closeHourCell);

                $("#hourTable").append(row);
              });
            }
        });
  }

  $(document).ready(function () {

    if($('#select_status').val() == 2)
    {
      $('#open_hour').hide();
      $('#close_hour').hide();
    }

    $('#select_status').change(function(){
      if(this.value == 2)
      {
        $('#open_hour').hide();
        $('#close_hour').hide();
      }
      else
      {
        $('#open_hour').show();
        $('#close_hour').show();
      }
    });

    $('.alert').delay(5000).fadeOut();

    
    $('#org_dropdown').change(function() {   
       loadHourTable();
    })

    $('#org_dropdown option').each(function() {
        // Check if the option value matches the organization ID
        if ($(this).val() == {{$koperasi->organization_id}}) {
            // Set the selected attribute for the matching option
            $(this).prop('selected', true);
            // Set the value of the hidden input field
            
        }
    });
    loadHourTable();
  });

</script>

@endsection
