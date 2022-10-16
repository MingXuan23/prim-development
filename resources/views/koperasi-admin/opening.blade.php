@extends('layouts.master')

@section('css')

@include('layouts.datatable');

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
  <div class="col-sm-6">
      <div class="page-title-box">
          <h4 class="font-size-18">{{ $koperasi->nama }}</h4>
          <ol class="breadcrumb mb-0">
              <!-- <li class="breadcrumb-item active">Restoran >> Kemas Kini Pembukaan</li> -->
          </ol>
      </div>
  </div>
</div>

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">

        <form method="POST" action="{{route('koperasi.storeOpening')}}" class="form-validation">
          {{csrf_field()}}

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

          <div class="form-group mb-0">
            <div class="text-right">

                <a type="button" href=""
                    class="btn btn-secondary waves-effect waves-light mr-1">
                    Kembali
                </a>
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

          <tbody>
          @foreach($hour as $hour)
          <tr>
            <td> 
            @if($hour->day ==1)
            Isnin
            @elseif($hour->day ==2)
            Selasa
            @elseif($hour->day ==3)
            Rabu
            @elseif($hour->day ==4)
            Khamis
            @elseif($hour->day ==5)
            Jumaat           
            @elseif($hour->day ==6)
            Sabtu            
            @elseif($hour->day ==0)
            Ahad
            @endif
            </td>

            <td>
            @if($hour->status== 2 ) 
            <div class="d-flex justify-content-center"><span class="badge badge-danger">tutup</span></div>
            @elseif($hour->status== 1)
            <div class="d-flex justify-content-center"><span class="badge badge-success">buka</span></div>
            @endif
            </td>
            
            <td>
              {{$hour->open_hour}}
            </td>
            
            <td>
              {{$hour->close_hour}}
            </td>

          </tr>
          @endforeach
    
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

  });

</script>

@endsection
