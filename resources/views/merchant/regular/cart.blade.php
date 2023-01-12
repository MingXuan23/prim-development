@extends('layouts.master')

@section('css')

<link href="{{ URL::asset('assets/css/required-asterick.css')}}" rel="stylesheet">
{{-- <link href="{{ URL::asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.css') }}" rel="stylesheet"> --}}
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<link rel="stylesheet" href="{{ URL::asset('assets/css/datatable.css')}}">
@include('layouts.datatable')

<style>
.noborder{
  border: none!important;
}

#img-size
{
  width: 100px;
  height: 100px;
  object-fit: cover;
}

.loading {
  width: 35px;
  height: 35px;
  display:none;
}

</style>

@endsection

@section('content')

<div class="container">
  <div class="row d-flex justify-content-center align-items-center">
    <div class="col">
      <div class="d-flex justify-content-center align-items-center">
        <span class="h2 m-4">Senarai Pesanan</span>
      </div>

      <div class="card">
        <div class="card-body">

          @if(Session::has('success'))
            <div class="alert alert-success">
              <p>{{ Session::get('success') }}</p>
            </div>
          @elseif(Session::has('error'))
            <div class="alert alert-danger">
              <p>{{ Session::get('error') }}</p>
            </div>
          @endif

          <input type="hidden" name="cart_id" id="cart_id" value="@if($cart){{ $cart->id }}@endif">

          <div class="table-responsive">
            <table class="table table-borderless responsive" id="cartTable" width="100%" cellspacing="0">
                <thead class="thead-dark">
                    <tr class="text-center">
                      <th style="width: 25%">Nama</th>
                      <th style="width: 15%">Kuantiti</th>
                      <th style="width: 25%">Kuantiti Penuh</th>
                      <th style="width: 20%">Harga Satu Unit (RM)</th>
                      <th style="width: 15%">Buang</th>
                    </tr>
                </thead>
            </table>
          </div>

        </div>
      </div>

      @if($cart)
        <div class="card mb-4 border">
          <div class="card-body p-4">
            <div class="table-responsive">
              <table class="table table-borderless mb-0">
                  <tbody>
                    <tr>
                      <th class="text-muted" scope="row">Jumlah Kasar:</th>
                      <td class="lead">RM {{ number_format((double)($cart->total_price - $response->fixed_charges), 2, '.', '') }}</td>
                    </tr>
                    @if($response->fixed_charges != null)<tr>
                      <th class="text-muted" scope="row">Cas Servis:</th>
                      <td class="lead">RM {{ number_format((double)$response->fixed_charges, 2, '.', '') }}</td>
                    </tr>@endif
                    <tr>
                      <th class="text-muted" scope="row">Jumlah Keseluruhan:</th>
                      <td class="lead">RM {{ number_format((double)$cart->total_price, 2, '.', '') }}</td>
                    </tr>
                  </tbody> 
              </table>
            </div>
          </div>
        </div>
        
      <form action="{{ route('merchant-reg.store-order', ['org_id' => $cart->org_id, 'order_id' => $cart->id]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="card mb-4 border">
          <div class="card-body p-4">

            <div class="row">
              <div class="col">
                <div class="form-group required">
                  <label>Jenis Pesanan</label>
                  <select class="form-control" data-parsley-required-message="Sila pilih jenis pesanan" id="order_type" required>
                    <option value="" @if($cart->order_type == null) selected @endif>Pilih Jenis Pesanan</option>
                    {{-- <option value="Delivery">Penghantaran</option> --}}
                    <option value="Pick-Up" @if($cart->order_type == 'Pick-Up') selected @endif>Ambil Sendiri</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="row">
              
              <input type="hidden" id="org_id" value="{{ $cart->org_id }}">

              <div class="col pickup-date-div" hidden>
                <div class="form-group required">
                  <label>Tarikh Pengambilan</label>
                  <input type="text" value="{{ $response->pickup_date }}" class="form-control" name="pickup_date" id="datepicker"  placeholder="Pilih tarikh" readonly required>
                </div>
              </div>

              <div class="col pickup-time-div" hidden>
                <div class="form-group required">
                  <label>Masa Pengambilan</label>
                  <div class="timepicker-section">
                    <input type="time" value="{{ $response->pickup_time }}" class="form-control" name="pickup_time" id="timepicker" required>
                    <p class="time-range"></p>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>

        <div class="card mb-4 border">
          <div class="card-body p-4">
            <div class="row">
              <div class="col">
                <div class="form-group">
                  <label>Nota kepada Peniaga</label>
                  <textarea class="form-control" name="note" placeholder="Optional">{{ $cart->note }}</textarea>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <input type="hidden" name="order_type" id="hidden_order_type" value="{{ $cart->order_type }}">
        
        <div class="row mb-2">
          <div class="col d-flex justify-content-end">
            <a href="{{ route('merchant-reg.show', $cart->org_id) }}" type="button" class="btn-lg btn-light mr-2">Kembali</a>
            <button type="submit" class="btn-lg btn-primary">Teruskan</button>
          </div>
        </div>
      </form>
      @else
      <div class="d-flex justify-content-center">
        <a href="{{ route('merchant-reg.show', $response->org_id) }}" type="button" class="btn-lg btn-light mr-2">Kembali</a>
      </div>
      @endif
    </div>
  </div>
</div>

{{-- Delete Confirmation Modal --}}
<div id="deleteConfirmModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
      <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title"><i class="fas fa-info-circle"></i>  Buang Item</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <div class="modal-body">
            Anda pasti mahu buang item ini?
          </div>
          <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="btn btn-light">Tidak</button>
            <img class="loading" src="{{ URL('images/koperasi/loading-ajax.gif')}}">
            <button type="button" data-dismiss="modal" class="btn btn-danger" id="delete_confirm_item">Buang</button>
          </div>
      </div>
  </div>
</div>
  {{-- end Delete Confirmation Modal --}}



@endsection

@section('script')

<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
{{-- <script src="{{ URL::asset('assets/libs/bootstrap-datepicker/bootstrap-datepicker.min.js') }}" defer></script> --}}

<script>
  $(document).ready(function(){
    let org_id = $('input#org_id').val()
    let init_order_type = $('#hidden_order_type').val()
    let cId = $('input#cart_id').val()
    
    if(init_order_type == 'Pick-Up'){
      $('.pickup-date-div').removeAttr('hidden')
      dateOnChange()
    } else {
      $('.pickup-date-div').attr('hidden', true)
      $('.pickup-time-div').attr('hidden', true)
    }

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    fetch_data(cId)

    function fetch_data(cId = '') {
        cartTable = $('#cartTable').DataTable({
            "searching": false,
            "bLengthChange": false,
            "bPaginate": false,
            "info": false,
            "orderable": false,
            "ordering": false,
            processing: true,
            serverSide: true,
            "language": {
                "zeroRecords": "Tiada Item Buat Masa Sekarang."
            },
            ajax: {
                url: "{{ route('merchant-reg.get-all-items') }}",
                data: {
                    id:cId,
                    type:'cart',
                    "_token": "{{ csrf_token() }}",
                },
                type: 'GET',
            },
            'columnDefs': [{
                "targets": [0, 1, 2, 3, 4], // your case first column
                "className": "align-middle text-center", 
            },
            { "responsivePriority": 1, "targets": 0 },
            { "responsivePriority": 2, "targets": 2 },
            { "responsivePriority": 3, "targets": 4 },
            ],
            columns: [{
                data: "name",
                name: 'name',
                orderable: false,
                searchable: false,
            }, {
                data: "quantity",
                name: 'quantity',
                orderable: false,
                searchable: false,
            }, {
                data: "full_quantity",
                name: 'full_quantity',
                orderable: false,
                searchable: false,
            },{
                data: 'price',
                name: 'price',
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

    $('#order_type').change(function() {
      let type_val = $(this).children(':selected').val()
      $('#hidden_order_type').val(type_val)
      if(type_val == 'Pick-Up') {
        $('.pickup-date-div').removeAttr('hidden')
      } else {
        $('.pickup-date-div').attr('hidden', true)
        $('.pickup-time-div').attr('hidden', true)
      }
    })

    $('#datepicker').change(function() {
      dateOnChange()
    })

    function dateOnChange() {
      let date_val = $('#datepicker').val(), timePicker = $('#timepicker'), timeRange = $('.time-range')
      if(date_val != null) {
        $('.pickup-time-div').removeAttr('hidden')
        $.ajax({
          url: '{{ route("merchant-reg.fetch-hours") }}',
          method: 'POST',
          data: {org_id:org_id, date:date_val, "_token": "{{ csrf_token() }}",},
          beforeSend:function() {
            timeRange.empty()
          },
          success:function(result) {
            if(result.hour.open) {
              timePicker.prop('disabled', false)
              timePicker.attr('min', result.hour.min)
              timePicker.attr('max', result.hour.max)
              timeRange.append(result.hour.body)
            } else {
              timePicker.prop('disabled', true)
              timeRange.append(result.hour.body)
            }
          },
          error:function(result) {
            console.log(result.responseText)
          }
        })
      } else {
        $('.pickup-time-div').attr('hidden', true)
      }
    }
    
    let order_cart_id = null

    $(document).on('click', '.delete-item', function(){
      order_cart_id = $(this).attr('data-cart-order-id')
      $('#deleteConfirmModal').modal('show')
    })

    $(document).on('click', '#delete_confirm_item', function(){
      $.ajax({
        url: "{{ route('merchant-reg.destroy-item') }}",
        method: "DELETE",
        data: {cart_id:order_cart_id, "_token": "{{ csrf_token() }}",},
        beforeSend:function() {
          $('.loading').show()
          $(this).hide()
          $('.alert-success').empty()
        },
        success:function(result) {
          $('.loading').hide()
          $(this).show()
          
          location.reload()
        },
        error:function(result) {
          console.log(result.responseText)
        }
      })
    })

    var dates = []
    
    $(document).ready(function() {
      $.ajax({
        url: '{{ route("merchant-reg.disabled-dates") }}',
        method: 'post',
        data: {org_id:org_id, "_token": "{{ csrf_token() }}",},
        success:function(result) {
          $.each(result.dates, function(index, value) {
            dates.push(value)
          })
        },
        error:function(result) {
          console.log(result.responseText)
        }
      })
    })
    

    $("#datepicker").datepicker({
        minDate: 1,
        maxDate: '+1m',
        dateFormat: 'mm/dd/yy',
        dayNamesMin: ['Ahd', 'Isn', 'Sel', 'Rab', 'Kha', 'Jum', 'Sab'],
        beforeShowDay: editDays
    })

    disabledDates = dates
    
    function editDays(date) {
      for (var i = 0; i < disabledDates.length; i++) {
        if (new Date(disabledDates[i]).toString() == date.toString()) {             
          return [false];
        }
      }
      return [true];
    }

    $('.alert-success').delay(2000).fadeOut()
    $('.alert-danger').delay(4000).fadeOut()

  });
</script>

@endsection