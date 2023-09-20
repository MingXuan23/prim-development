@extends('layouts.master')

@section('css')

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Promosi</h4>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Promosi >> Set Promosi</li>
            </ol>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">
            
        @if(Session::has('success'))
            <div class="alert alert-success">
              <p>{{ Session::get('success') }}</p>
            </div>
          @elseif(Session::has('error'))
            <div class="alert alert-danger">
              <p>{{ Session::get('error') }}</p>
            </div>
          @endif

            <form method="post" action="{{route('homestay.insertpromotion')}}" enctype="multipart/form-data"
                class="form-validation">
                {{csrf_field()}}
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <div class="form-group required">
                                <label class="control-label">Nama Homestay <span style="color:#d00"> *</span></label>
                                <select name="homestayid" id="homestayid" class="form-control"
                                    data-parsley-required-message="Sila pilih status homestay" required>
                                    <option selected>Pilih Homestay</option>
                                    @foreach($data as $rows)
                                    <option value="{{ $rows->id }}">{{ $rows->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                    </div>

                    <div class="row">
                        <div class="col">
                    <div class="form-group">
                        <label class="control-label"> Nama Promosi <span style="color:#d00"> *</span> </label>
                        <input type="text" name="promotionname" id="promotionname" class="form-control" placeholder="Nama Promosi"
                            data-parsley-required-message="Sila masukkan nama promosi" required>
                            </input>
                    </div>
                    </div>
                    </div>
                    
                    <div class="row">
                        
                        <div class="col">
                            <div class="form-group required">
                                <label class="control-label">Tarikh Dari <span style="color:#d00"> *</span></label>
                                <input type="text" class="form-control" id="datefrom" name="datefrom">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group required">
                                <label class="control-label">Tarikh Hingga </label>
                                <input type="text" class="form-control" id="dateto" name="dateto">
                            </div>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col">
                    <div class="form-group">
                        <label class="control-label"> Diskaun Dikenakan (%) <span style="color:#d00"> *</span> </label>
                        <input type="text" name="discount" id="discount" class="form-control" placeholder="Diskaun Dikenakan (%)"
                            data-parsley-required-message="Sila masukkan jumlah diskaun" required>
                            </input>
                    </div>
                    </div>
                    </div>
                   

                   
                    
                    <div class="form-group mb-0">
                        <div class="text-right">
                            <a type="button" href="{{ url()->previous() }}"
                                class="btn btn-secondary waves-effect waves-light mr-1">
                                Kembali
                            </a>
                            <button type="submit" class="btn btn-primary waves-effect waves-light mr-1">
                                Simpan
                            </button>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection


@section('script')
<script src="{{ URL::asset('assets/libs/parsleyjs/parsleyjs.min.js')}}"></script>
<script src="{{ URL::asset('assets/libs/inputmask/inputmask.min.js')}}"></script>
<script src="{{ URL::asset('assets/libs/jquery-mask/jquery.mask.min.js')}}"></script>


<script>

$(document).ready(function () {

    $('#homestayid').on('change', function() {
        var id = $(this).val();
        var today = new Date();
    var maxDate = new Date(today.getFullYear() + 1, today.getMonth(), today.getDate());

    // Fetch disabled dates
     $.ajax({
         url: "/disabledatepromo/" + id, // Use the appropriate URL for the disabledate route
         type: "GET",
         success: function(response) {
             var disabledDates = response.disabledDates;

             $("#datefrom, #dateto").datepicker("destroy");
            
             $("#datefrom").datepicker({
                minDate: 0,
                maxDate: maxDate,
                dateFormat: "yy-mm-dd",
                beforeShow: function(input, inst) {
                    inst.dpDiv.css({
                        "background-color": "#dce0df"
                    });
                },
                beforeShowDay: function(date) {
                    var string = jQuery.datepicker.formatDate('yy-mm-dd', date);
                    var isDisabled = (disabledDates.indexOf(string) !== -1);
                    return [!isDisabled];
                },
                onSelect: function(selectedDate) {
                    $("#dateto").datepicker("option", "minDate", selectedDate);
                }
            });

            $("#dateto").datepicker({
                minDate: 0,
                maxDate: maxDate,
                dateFormat: "yy-mm-dd",
                beforeShow: function(input, inst) {
                    inst.dpDiv.css({
                        "background-color": "#dce0df"
                    });
                },
                beforeShowDay: function(date) {
                    var string = jQuery.datepicker.formatDate('yy-mm-dd', date);
                    var isDisabled = (disabledDates.indexOf(string) !== -1);
                    return [!isDisabled];
                },
                onSelect: function(selectedDate) {
                    $("#datefrom").datepicker("option", "maxDate", selectedDate);
                }
            });
         },
         error: function() {
             // Handle error
         }
     });
    });
        $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
        $('.alert').delay(3000).fadeOut()
    });

</script>
@endsection