@extends('layouts.master')

@section('css')

@include('layouts.datatable');

@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Homestay</h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div>
                <a style="margin: 19px; float: right;" href="{{ route('homestay.createhomestay') }}"
                    class="btn btn-primary"> <i class="fas fa-plus"></i> Tambah Homestay</a>
            </div>

            <div class="card-body">


                <div class="flash-message"></div>
                <div class="table-responsive">
                    <table id="homestaytable" class="table table-bordered table-striped dt-responsive wrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr style="text-align:center">
                                <th hidden>Homestay ID</th>
                                <th>Nama Homestay</th>
                                <th>Lokasi</th>
                                <th>No Telefon</th>
                                <th>Status</th>
                                <th>Set Promosi</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $record)
                        <tr>
                            <td hidden>{{ $record->homestayid }}</td>
                            <td style="width: 200px;">{{ $record->name }}</td>
                            <td style="width: 450px;">{{ $record->location }}</td>
                            <td>{{ $record->pno }}</td>
                            <td>{{ $record->status }}</td>
                            <td><button type="button" class="btn btn-success" id="promo">Set</button></td>
                            <td style="width: 200px;">
                            <button type="button" class="btn btn-primary" id="addroom">Add Rooms</button>
                            <button class="btn btn-success" id="editbutton">Edit</button>
                            </td>
                    </tr>
                    @endforeach
                    </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="modal fade" id="promomodal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Promotions</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
              <form class="row g-3" id="promoform" method="POST" action="">
    @csrf
          
    <input type="text" class="form-control" name="id" id="id" hidden>
    <div class="col-12">
        <label class="form-label">Promotions Name:</label>
        <input type="text" class="form-control" id="promotionname" name="promotionname">
    </div>

    <div class="col-md-6">
        <label class="form-label">Date From:</label>
        <input type="text" class="form-control" id="datefrom" name="datefrom">
    </div>

    <div class="col-md-6">
        <label class="form-label">Date To:</label>
        <input type="text" class="form-control" id="dateto" name="dateto">
    </div>


    <div class="col-md-6">
        <label class="form-label">Discount (%)</label>
        <input type="text" class="form-control" id="discount" name="discount">
    </div>

    <div class="col-12">
        <button type="submit" class="btn btn-primary">Save Changes</button>
    </div>
</form>
              </div>
              <div class="modal-footer">
              <a href="homestay" class="btn btn-secondary" id="homestay">Close</a>
              </div>
            </div>
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

        $(document).on('click', '#promo', function(e) {
    e.preventDefault(); // Prevent the default form submission
    $('#promomodal').modal('show');
    id = e.target.parentElement.previousElementSibling.previousElementSibling.previousElementSibling.previousElementSibling.previousElementSibling.innerText;
    $('#id').val(id);

    var today = new Date();
    var maxDate = new Date(today.getFullYear() + 1, today.getMonth(), today.getDate());

    // Fetch disabled dates
     $.ajax({
         url: "/disabledatepromo/" + id, // Use the appropriate URL for the disabledate route
         type: "GET",
         success: function(response) {
             var disabledDates = response.disabledDates;
            
            $("#datefrom, #dateto").datepicker({
                minDate: 0,
                maxDate: maxDate,
                dateFormat: "yy-mm-dd",
                beforeShowDay: function(date) {
                    var string = jQuery.datepicker.formatDate('yy-mm-dd', date);
                    var isDisabled = (disabledDates.indexOf(string) !== -1);
                    return [!isDisabled];
                }
                
            });
         },
         error: function() {
             // Handle error
         }
     });

});

        $('.alert').delay(3000).fadeOut()
});

</script>
@endsection