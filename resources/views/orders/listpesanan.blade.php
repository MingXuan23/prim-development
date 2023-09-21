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
                <h4 class="font-size-18">Urus Pesanan >> {{ $nama }}</h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
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
                    <div class="flash-message"></div>
                    <div class="table-responsive">
                        <table id="menutable" class="table table-bordered table-striped dt-responsive wrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr style="text-align:center">
                                    <th hidden>Order ID</th>
                                    <th>Dish Name</th>
                                    <th>Quantity</th>
                                    <th>Delivery Date</th>
                                    <th>Delivery Time</th>
                                    <th>Address</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $list)
                                <tr>
                                    <td hidden>{{ $list->id }}</td>
                                    <td>{{ $list->dishname }}</td>
                                    <td>{{ $list->quantity }}</td>
                                    <td>{{ $list->date }}</td>
                                    <td>{{ $list->time }}</td>
                                    <td>{{ $list->delivery_address }}</td>
                                    <td>{{ $list->delivery_status }}</td>
                                    <td><button class="btn btn-success" id="editbutton">Edit Status</button></td>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="menumodal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Status Pesanan</h1>
                        </div>
                        <div class="modal-body" style="border-bottom: 1px solid #ccc; border-top: 1px solid #ccc;">
                            <form class="row g-3" id="menuform" method="POST" action="">
                                @csrf
                                <input type="text" class="form-control" name="orderid" id="orderid" hidden>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" id="status" class="form-control" required>
                                        <option value="" selected disabled>Pilih Status</option>
                                        <option value="Pending">Pending</option>
                                        <option value="Pending">Preparing</option>
                                        <option value="Pending">Delivering</option>
                                        <option value="Pending">Delivered</option>
                                    </select>
                                </div>

                                <div class="col-12 d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <a href="{{ route('orders.listpesanan', ['id' => $organizationId]) }}" class="btn btn-secondary" id="">Kembali</a>
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
        
            $('#menutable').DataTable();

            $(document).on('click', '#editbutton', function(e) {
                status = e.target.parentElement.previousElementSibling.innerText;
                orderid = e.target.parentElement.previousElementSibling.previousElementSibling.previousElementSibling.previousElementSibling.previousElementSibling.previousElementSibling.previousElementSibling.innerText;
                
                console.log(orderid);
                console.log(status);
                $('#orderid').val(orderid);
                $('#status').val(status);
                
                $('#menuform').attr('action', '/orders/editpesanan');
                $('#menumodal').modal('show');
            });

            $('.alert').delay(3000).fadeOut()
        });
    </script>
@endsection