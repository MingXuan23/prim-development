@extends('layouts.master')

@section('css')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
@include('layouts.datatable');

@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">{{ $nama }}</h4>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Buat Tempahan >> {{ $nama }}</li>
            </ol>
        </div>
    </div>
</div>
<div class="row">
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

         @if(Session::has('success'))
            <div class="alert alert-success">
              <p>{{ Session::get('success') }}</p>
            </div>
          @elseif(Session::has('error'))
            <div class="alert alert-danger">
              <p>{{ Session::get('error') }}</p>
            </div>
          @endif

                <div class="flash-message"></div>
                <div class="table-responsive">
                    <table id="homestaytable" class="table table-bordered table-striped dt-responsive wrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr style="text-align:center">
                                <th hidden>Room ID</th>
                                <th>Nama Bilik</th>
                                <th>Kapasiti Bilik</th>
                                <th>Detail Bilik</th>
                                <th>Harga Semalam (RM)</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $record)
                        <tr>
                            <td hidden>{{ $record->roomid }}</td>
                            <td>{{ $record->roomname }}</td>
                            <td>{{ $record->roompax }}</td>
                            <td>{{ $record->details }}</td>
                            <td>{{ $record->price }}</td>
                            <td><button class="btn btn-success" id="bookbutton">Tempah Sekarang</button></td>
                            </td>
                    </tr>
                    @endforeach
                    </tbody>
                    </table>
                </div>
            </div>
        </div>


<div class="modal fade" id="bookmodal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Tempah Bilik</h1>
            </div>
            <div class="modal-body" style="border-bottom: 1px solid #ccc; border-top: 1px solid #ccc;">
                <form class="row g-3" id="bookform" method="POST" action="">
                    @csrf
                    <input type="text" class="form-control" name="roomid" id="roomid" hidden>
                    <div class="col-12 mb-3">
                        <label class="form-label">Nama Bilik</label>
                        <input type="text" class="form-control" id="roomname" name="roomname" disabled>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tarikh Dari</label>
                        <input type="text" class="form-control" id="checkin" name="checkin">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tarikh Hingga</label>
                        <input type="text" class="form-control" id="checkout" name="checkout">
                    </div>

                    <div class="col-12 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <a href="{{ route('homestay.bookhomestay', ['id' => $homestayid]) }}" class="btn btn-secondary" id="homestay">Kembali</a>
            </div>
        </div>
    </div>
</div>

<div class="form-group mb-0">
                        <div class="text-right">
                            <a type="button" href="{{ url()->previous() }}"
                                class="btn btn-secondary waves-effect waves-light mr-1">
                                Kembali
                            </a>
                        </div>
                    </div>


    </div>
</div>


@endsection


@section('script')
<!-- Peity chart-->
<script src="{{ URL::asset('assets/libs/peity/peity.min.js')}}"></script>

{{-- <script src="{{ URL::asset('assets/js/pages/dashboard.init.js')}}"></script> --}}

<script>
    $(document).ready(function() {
    
        $('#homestaytable').DataTable();

$(document).on('click', '#bookbutton', function(e) {
    price = e.target.parentElement.previousElementSibling.innerText;
    roomname = e.target.parentElement.previousElementSibling.previousElementSibling.previousElementSibling.previousElementSibling.innerText;
    id = e.target.parentElement.previousElementSibling.previousElementSibling.previousElementSibling.previousElementSibling.previousElementSibling.innerText;
    console.log(id);
    $('#roomid').val(id);
    $('#roomname').val(roomname);

    var today = new Date();
    var maxDate = new Date(today.getFullYear() + 1, today.getMonth(), today.getDate());

    // Fetch disabled dates
     $.ajax({
         url: "/disabledateroom/" + id, // Use the appropriate URL for the disabledate route
         type: "GET",
         success: function(response) {
             var disabledDates = response.disabledDates;

             $("#checkin, #checkout").datepicker("destroy");
            
             $("#checkin").datepicker({
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
                    $("#checkout").datepicker("option", "minDate", selectedDate);
                }
            });

            $("#checkout").datepicker({
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
                    $("#checkin").datepicker("option", "maxDate", selectedDate);
                }
            });
         },
         error: function() {
             // Handle error
         }
     });
    
    $('#bookform').attr('action','insertbooking/'+id + '/' + price);
    $('#bookmodal').modal('show');
  });

$('.alert').delay(3000).fadeOut()
});

</script>
@endsection