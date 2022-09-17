@extends('layouts.master')

@section('css')

@include('layouts.datatable');

@endsection

@section('content')

<div class="card mb-3">
    <div class="card-header">
      <i class="ti-email mr-2"></i>Pesanan Untuk Diambil</div>
    <div class="card-body">
      @if(Session::has('success'))
        <div class="alert alert-success">
          <p>{{ Session::get('success') }}</p>
        </div>
      @endif
      <div class="table-responsive">
        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
  
          <thead>
            <tr>
              <th style="width: 2%">No.</th>
              <th style="width: 15%">Nama Koperasi</th>
              <th style="width: 10%">No Telefon Koperasi</th>
              <th style="width: 10%">Tarikh dan Waktu Pesan</th>
              <th style="width: 10%">Tarikh Pengambilan</th>
              <th style="width: 15%">Nota</th>
              <th style="width: 10%">Jumlah (RM)</th>
              <th style="width: 10%">Status</th>
              <th style="width: 15%">Action</th>
            </tr>
          </thead>
  
          <tbody>
            @php($i = 1)
            @if(count($order) != 0)
              @foreach($order as $row)
              @php($date = date_create($row->updated_at))
              @php($pickup = date_create($row->pickup_date))
              @csrf
                <tr>
                  <td class="align-middle">{{ $i }}.</td>
                  <td class="align-middle">{{ $row->koop_name }}</td>
                  <td class="align-middle">{{ $row->koop_telno }}</td>
                  <td class="align-middle">{{ date_format($date,"M D Y, h:m:s A") }}</td>
                  <td class="align-middle">{{ date_format($pickup,"D, M d Y") }}</td>
                  <td class="align-middle">
                    @if($row->note != null)
                    {{ $row->note }}
                    @else
                    <i>Tiada Nota</i>
                    @endif
                  </td>
                  <td class="align-middle">
                    {{ number_format($row->total_price, 2, '.', '') }} |
                    <a href="{{ route('koperasi.list', $row->id) }}">Lihat Pesanan</a>
                  </td>
                  <td class="align-middle text-center">
                    @if($row->status == 2)
                    <span class="badge rounded-pill bg-warning ">Sedang Diproses</span>
                    @else
                    <span class="badge rounded-pill bg-danger text-white">Tidak Diambil</span>
                    @endif
                  </td>
                  <td class="align-middle">
                    <div class="row d-block m-1">
                      <button type="button" id="{{ $row->id }}" class="btn btn-warning btn-block">Pilih Hari Lain</button>
                    </div>
                    <div class="row d-block m-1">
                      <button type="button" id="{{ $row->id }}" class="btn btn-danger btn-block">Buang</button>
                    </div>
                  </td>
                </tr>
              @php($i++)
              @endforeach
            @else
              <tr>
                <td colspan="9" class="text-center"><i>Tiada Pesanan Buat Masa Sekarang.</i></td>
              </tr>
            @endif
          </tbody>
        </table>
      </div>
      <div class="row mt-2 ">
        <div class="col d-flex justify-content-end">
          {{ $order->links() }}
        </div>
      </div>
    </div>
    {{-- <div class="card-footer small text-muted"></div> --}}
</div>

<!-- Modal -->
<div class="modal fade" id="dayModal" role="dialog">
  <div class="modal-dialog">
  
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Pilih Hari Lain</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col">
            <div class="form-group required">
              <label class="col">Hari Pengambilan</label>
              <div class="col" >
                <select class="form-control" data-parsley-required-message="Sila pilih hari" id="pick_up_date" required>
                  <option value="" selected>Pilih Hari</option>
                </select>
              </div>
            </div>
          </div>
        </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light mr-2" data-dismiss="modal">Tutup</button>
        <button type="button" class="btn btn-primary" id="date_submit" data-dismiss="modal">Submit</button>
      </div>
    </div>
    
    </div>
  </div>
</div>

{{-- confirmation delete modal --}}
<div id="deleteConfirmationModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
      <div class="modal-content">
          <div class="modal-header">
              <h4 class="modal-title">Batalkan Pesanan</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
              Adakah anda pasti?
          </div>
          <div class="modal-footer">
              <button type="button" data-dismiss="modal" class="btn btn-primary" id="delete"
                  name="delete">Batal...</button>
              <button type="button" data-dismiss="modal" class="btn">Kembali</button>
          </div>
      </div>
  </div>
</div>
{{-- end confirmation delete modal --}}

@endsection

@section('script')

<script src="{{ URL::asset('assets/libs/peity/peity.min.js')}}"></script>

<script>
  $(document).ready(function(){
    var o_id;

    $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    $('.btn-warning').click(function(e) {
      e.preventDefault();
      order_id = $(this).attr('id');

      $.ajax({
        url: "{{ route('koperasi.fetchDay') }}",
        method: "GET",
        data: {oID:order_id},
        success:function(result)
        {
          o_id = order_id;
          $('#dayModal').modal('show');
          var select_day = $('#pick_up_date');
          select_day.empty();
          select_day.append("<option value='' disabled selected>Pilih Hari</option>");

          $.each(result.day, function(key, value){
            // console.log(value.day);
            $.each(result.past, function(i, v){
                if     (value.day == 1 && i == 1) {select_day.append("<option value='"+i+"'>Isnin "+v+"</option>")  }
                else if(value.day == 2 && i == 2) {select_day.append("<option value='"+i+"'>Selasa "+v+"</option>") }
                else if(value.day == 3 && i == 3) {select_day.append("<option value='"+i+"'>Rabu "+v+"</option>")   }
                else if(value.day == 4 && i == 4) {select_day.append("<option value='"+i+"'>Khamis "+v+"</option>") }
                else if(value.day == 5 && i == 5) {select_day.append("<option value='"+i+"'>Jumaat "+v+"</option>") }
                else if(value.day == 6 && i == 6) {select_day.append("<option value='"+i+"'>Sabtu "+v+"</option>")  }
                else if(value.day == 0 && i == 0) {select_day.append("<option value='"+i+"'>Ahad "+v+"</option>")   }
            })
            
          })
          // console.log(result);
        },
        error:function(result)
        {
          console.log(result);
        }
      })
    });

    $('#date_submit').click(function(e) {
      e.preventDefault();
      var date_val = $('#pick_up_date').children(":selected").val();

      if(!date_val)
      {
        alert('null');
      }
      else
      {
        $.ajax({
          url: "{{ route('koperasi.updatePickUpDate') }}",
          method: "POST",
          dataType: 'html',
          data: {
            "_token": "{{ csrf_token() }}",
            oID:o_id,
            day:date_val
          },
          success:function(result)
          { 
            setTimeout(function() {
                $('#dayModal').modal('hide');
            }, 2000);

            $('div.flash-message').html(result);

            location.reload();
            // console.log(result);
          },
          error:function(result)
          {
            $('div.flash-message').html(result);

            // console.log(result);
          }
        })
      }

    });

    $(document).on('click', '.btn-danger', function(){
        o_id = $(this).attr('id');
        $('#deleteConfirmationModal').modal('show');
    });

    $('#delete').click(function() {
          $.ajax({
              type: 'POST',
              dataType: 'html',
              data: {
                  "_token": "{{ csrf_token() }}",
                  _method: 'DELETE',
                  oID: o_id,
              },
              url: "/koperasi/order/" + o_id,
              success: function(result) {
                setTimeout(function() {
                    $('#deleteConfirmationModal').modal('hide');
                }, 2000);

                $('div.flash-message').html(result);

                location.reload();
                // console.log(result);
              },
              error: function (result) {
                $('div.flash-message').html(result);
              }
          })
        });

    $('.alert').delay(2000).fadeOut();

    
  });
</script>

@endsection