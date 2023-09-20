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
                <h4 class="font-size-18">Urus Menu</h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div>
                    <a style="margin: 19px; float: right;" href="{{ route('homestay.setpromotion') }}"
                        class="btn btn-primary"> <i class="fas fa-plus"></i> Tambah Menu </a>
                </div>

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
                    <div class="flash-message"></div>
                    <div class="table-responsive">
                        <table id="homestaytable" class="table table-bordered table-striped dt-responsive wrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr style="text-align:center">
                                    <th hidden>Promotion ID</th>
                                    <th>Promosi</th>
                                    <th>Nama Homestay</th>
                                    <th>Tarikh Dari</th>
                                    <th>Tarikh Hingga</th>
                                    <th>Diskaun (%)</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $record)
                                <tr>
                                    <td hidden>{{ $record->promotionid }}</td>
                                    <td>{{ $record->promotionname }}</td>
                                    <td>{{ $record->nama }}</td>
                                    <td>{{ $record->datefrom }}</td>
                                    <td>{{ $record->dateto }}</td>
                                    <td>{{ $record->discount }}</td>
                                    <td><button class="btn btn-success" id="editbutton">Edit</button></td>
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
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Promosi</h1>
                        </div>
                        <div class="modal-body" style="border-bottom: 1px solid #ccc; border-top: 1px solid #ccc;">
                            <form class="row g-3" id="promoform" method="POST" action="">
                                @csrf
                                <input type="text" class="form-control" name="promotionid" id="promotionid" hidden>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama Homestay</label>
                                    <input type="text" class="form-control" id="nama" name="nama" disabled>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama Promosi</label>
                                    <input type="text" class="form-control" id="promotionname" name="promotionname">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tarikh Dari</label>
                                    <input type="text" class="form-control" id="datefrom" name="datefrom">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tarikh Hingga</label>
                                    <input type="text" class="form-control" id="dateto" name="dateto">
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label">Diskaun (%)</label>
                                    <input type="text" class="form-control" id="discount" name="discount">
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

            $(document).on('click', '#editbutton', function(e) {
                discount = e.target.parentElement.previousElementSibling.innerText;
                dateto = e.target.parentElement.previousElementSibling.previousElementSibling.innerText;
                datefrom = e.target.parentElement.previousElementSibling.previousElementSibling.previousElementSibling.innerText;
                nama = e.target.parentElement.previousElementSibling.previousElementSibling.previousElementSibling.previousElementSibling.innerText;
                promotionname = e.target.parentElement.previousElementSibling.previousElementSibling.previousElementSibling.previousElementSibling.previousElementSibling.innerText;
                id = e.target.parentElement.previousElementSibling.previousElementSibling.previousElementSibling.previousElementSibling.previousElementSibling.previousElementSibling.innerText;
                console.log(id);
                $('#promotionid').val(id);
                $('#nama').val(nama);
                $('#promotionname').val(promotionname);
                $('#datefrom').val(datefrom);
                $('#dateto').val(dateto);
                $('#discount').val(discount);

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
                
                $('#promoform').attr('action','editpromo/'+id);
                $('#promomodal').modal('show');
            });

            $('.alert').delay(3000).fadeOut()
        });
    </script>
@endsection