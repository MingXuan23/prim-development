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
                            <button type="button" class="btn btn-primary" id="addroom">Tambah Bilik</button>
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
                <h1 class="modal-title fs-5" id="exampleModalLabel">Set Promosi</h1>
            </div>
            <div class="modal-body" style="border-bottom: 1px solid #ccc; border-top: 1px solid #ccc;">
                <form class="row g-3" id="promoform" method="POST" action="">
                    @csrf
                    <input type="text" class="form-control" name="id" id="id" hidden>
                    <div class="col-12 mb-3">
                        <label class="form-label">Nama Promosi:</label>
                        <input type="text" class="form-control" id="promotionname" name="promotionname" placeholder="Masukkan Nama Promosi">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tarikh Dari :</label>
                        <input type="text" class="form-control" id="datefrom" name="datefrom">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tarikh Hingga :</label>
                        <input type="text" class="form-control" id="dateto" name="dateto">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Diskaun Dikenakan (%)</label>
                        <input type="text" class="form-control" id="discount" name="discount" placeholder="Jumlah Diskaun">
                    </div>

                    <div class="col-12 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <a href="homestay" class="btn btn-secondary" id="homestay">Kembali</a>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Set Bilik</h1>
            </div>
            <div class="modal-body" style="border-bottom: 1px solid #ccc; border-top: 1px solid #ccc;">
                <form class="row g-3" id="roomform" method="POST" action="">
                    @csrf
                    <input type="text" class="form-control" name="homestayid" id="homestayid" hidden>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama Bilik</label>
                        <input type="text" class="form-control" id="roomname" name="roomname" placeholder="Nama / Nombor Bilik">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Bilangan Orang Sebilik</label>
                        <input type="text" class="form-control" id="roompax" name="roompax" placeholder="Contoh : 1 - 4 Orang">
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label">Maklumat Bilik</label>
                        <input type="text" class="form-control" id="details" name="details" placeholder="Contoh : 2 Bilik, 1 Bilik Air, Wifi, 2 Katil Queen Size, Parking Disediakan">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Harga Bilik (RM)</label>
                        <input type="text" class="form-control" id="roomprice" name="roomprice" placeholder="Harga Bilik Semalam (RM)">
                    </div>

                    <div class="col-12 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <a href="homestay" class="btn btn-secondary" id="homestay">Kembali</a>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="staticBackdrop" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Data Homestay</h1>
            </div>
            <div class="modal-body" style="border-bottom: 1px solid #ccc; border-top: 1px solid #ccc;">
                <form class="row g-3" id="homestayform" method="POST" action="">
                    @csrf
                    <input type="text" class="form-control" name="idhomestay" id="idhomestay" hidden>
                    <div class="col-12 mb-3">
                        <label class="form-label">Nama Homestay</label>
                        <input type="text" class="form-control" id="name" name="name">
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label">Lokasi</label>
                        <input type="text" class="form-control" id="location" name="location">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nombor Telefon</label>
                        <input type="text" class="form-control" id="pno" name="pno">
                    </div>

                    <div class="col-md-6 mb-3">
                    <label class="control-label">Status Homestay</label>
                                <select name="stat" id="stat" class="form-control">
                                    <option selected>Pilih Status Homestay</option>
                                    <option value="Available">Available</option>
                                    <option value="Disabled">Disabled</option>
                                </select>
                            </div>
                    

                    <div class="col-12 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <a href="homestay" class="btn btn-secondary" id="homestay">Kembali</a>
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
                beforeShow: function(input, inst) {

                inst.dpDiv.css({
                    "background-color": "#dce0df" // Change this value to your preferred font size

                });
            },
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

$(document).on('submit', '#promoform', function(e) {
    e.preventDefault(); // Prevent the default form submission

    console.log("Form submitted"); // Added console.log statement

    var promotionname = $('#promotionname').val();
    var datefrom = $('#datefrom').val();
    var dateto = $('#dateto').val();
    var discount = $('#discount').val();
    var id = $('#id').val();

    console.log("Room Name:", promotionname); // Added console.log statement
    console.log("Room Pax:", datefrom);
    console.log("Details:", dateto);
    console.log("Price:", discount); // Added console.log statement
    console.log("ID:", id); // Added console.log statement

    $.ajax({
        type: 'POST',
        url: '/addpromo/' + id,
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            promotionname: promotionname,
            datefrom: datefrom,
            dateto: dateto,
            discount: discount
        },
        success: function(response) {
            // Handle the success response
            console.log("Success:", response); // Added console.log statement
            window.location.href = '{{ route("homestay.index") }}';
        },
        error: function(error) {
            // Handle the error response
            console.log("Error:", error); // Added console.log statement
        }
    });
  });

  $(document).on('click', '#addroom', function(e) {
    e.preventDefault(); // Prevent the default form submission
    $('#exampleModal').modal('show');
    id = e.target.parentElement.previousElementSibling.previousElementSibling.previousElementSibling.previousElementSibling.previousElementSibling.previousElementSibling.innerText;
    $('#homestayid').val(id);
});

$(document).on('submit', '#roomform', function(e) 
{
    e.preventDefault(); // Prevent the default form submission

    console.log("Form submitted"); // Added console.log statement

    var roomname = $('#roomname').val();
    var roompax = $('#roompax').val();
    var details = $('#details').val();
    var roomprice = $('#roomprice').val();
    var id = $('#homestayid').val();

    console.log("Room Name:", roomname); // Added console.log statement
    console.log("Room Pax:", roompax);
    console.log("Details:", details);
    console.log("Price:", roomprice); // Added console.log statement
    console.log("ID:", id); // Added console.log statement

    $.ajax({
        type: 'POST',
        url: '/addroom/' + id,
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            roomname: roomname,
            roompax: roompax,
            details: details,
            roomprice: roomprice
        },
        success: function(response) {
            // Handle the success response
            console.log("Success:", response); // Added console.log statement
            window.location.href = '{{ route("homestay.index") }}';
        },
        error: function(error) {
            // Handle the error response
            console.log("Error:", error); // Added console.log statement
        }
    });


       
});

$(document).on('click', '#editbutton', function(e) {
    status = e.target.parentElement.previousElementSibling.previousElementSibling.innerText;
    pno = e.target.parentElement.previousElementSibling.previousElementSibling.previousElementSibling.innerText;
    loc = e.target.parentElement.previousElementSibling.previousElementSibling.previousElementSibling.previousElementSibling.innerText;
    name = e.target.parentElement.previousElementSibling.previousElementSibling.previousElementSibling.previousElementSibling.previousElementSibling.innerText;
    id = e.target.parentElement.previousElementSibling.previousElementSibling.previousElementSibling.previousElementSibling.previousElementSibling.previousElementSibling.innerText;
    console.log(id);
    $('#idhomestay').val(id);
    $('#name').val(name);
    $('#location').val(loc);
    $('#pno').val(pno);
    $('#stat').val(status);
    
    $('#homestayform').attr('action','edithomestay/'+id);
    $('#staticBackdrop').modal('show');
  });

$('.alert').delay(3000).fadeOut()
});

</script>
@endsection