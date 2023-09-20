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
                <h4 class="font-size-18">Urus Menu >> {{ $nama }}</h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div>
                    <a style="margin: 19px; float: right;" href="{{ route('orders.addmenu', ['id' => $organizationId]) }}"
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
                        <table id="menutable" class="table table-bordered table-striped dt-responsive wrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr style="text-align:center">
                                    <th hidden>Dish ID</th>
                                    <th>Dish Name</th>
                                    <th>Price (RM)</th>
                                    <th>Dish Type</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $list)
                                <tr>
                                    <td hidden>{{ $list->id }}</td>
                                    <td>{{ $list->dishname }}</td>
                                    <td>{{ $list->price }}</td>
                                    <td>{{ $list->dishtype }}</td>
                                    <td><button class="btn btn-success" id="editbutton">Edit</button></td>
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
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Menu</h1>
                        </div>
                        <div class="modal-body" style="border-bottom: 1px solid #ccc; border-top: 1px solid #ccc;">
                            <form class="row g-3" id="menuform" method="POST" action="">
                                @csrf
                                <input type="text" class="form-control" name="dishid" id="dishid" hidden>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama Menu</label>
                                    <input type="text" class="form-control" id="dishname" name="dishname">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Harga Menu (RM)</label>
                                    <input type="text" class="form-control" id="price" name="price">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Jenis Menu</label>
                                    <select name="dishtype" id="dishtype" class="form-control" required>
                                        <option selected disabled>Pilih Jenis Menu</option>
                                        @foreach($dishtype as $rows)
                                            <option value="{{ $rows->id }}">{{ $rows->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12 d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <a href="{{ route('orders.listmenu', ['id' => $organizationId]) }}" class="btn btn-secondary" id="">Kembali</a>
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
                dishtype = e.target.parentElement.previousElementSibling.innerText;
                price = e.target.parentElement.previousElementSibling.previousElementSibling.innerText;
                dishname = e.target.parentElement.previousElementSibling.previousElementSibling.previousElementSibling.innerText;
                dishid = e.target.parentElement.previousElementSibling.previousElementSibling.previousElementSibling.previousElementSibling.innerText;
                
                console.log(dishid);
                console.log(dishtype);
                $('#dishid').val(dishid);
                $('#dishname').val(dishname);
                $('#price').val(price);
                $('#dishtype').val(dishtype);
                
                $('#menuform').attr('action', '/orders/editmenu');
                $('#menumodal').modal('show');
            });

            $('.alert').delay(3000).fadeOut()
        });
    </script>
@endsection