@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Carian Derma</h4>
            <!-- <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Welcome to Veltrix Dashboard</li>
            </ol> -->
        </div>
    </div>
</div>
<div class="row">

    <div class="col-md-12">
        <div class="card card-primary">

            @if(count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                    <li>{{$error}}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{csrf_field()}}
            <div class="card-body">

                <div class="form-group">
                    <label>Nama Organisasi</label>
                    <select name="organization" id="organization" class="form-control">
                        <option value="" disabled selected>Pilih Organisasi</option>
                        @foreach($organization as $row)
                        <option value="{{ $row->id }}">{{ $row->nama }}</option>
                        @endforeach
                    </select>
                </div>


            </div>

            <div class="">
                <button onclick="filter()" style="float: right" type="submit" class="btn btn-primary"><i
                        class="fa fa-search"></i>
                    Tapis</button>
            </div>

        </div>
    </div>

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
                @if(\Session::has('success'))
                <div class="alert alert-success">
                    <p>{{ \Session::get('success') }}</p>
                </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <tr style="text-align:center">
                            <th> No. </th>
                            <th> Nama Derma </th>
                            <th> Penerangan </th>
                            <th> Harga (RM) </th>
                            <th> Status </th>
                            <th> Action </th>
                        </tr>

                        @foreach($donate as $row)
                        <tr>
                            <td>{{ $loop->iteration }}. </td>
                            <td>{{ $row['nama'] }} </td>
                            <td>{{ $row['description'] }}</td>
                            <td> {{ number_format($row['amount'] , 2) ?? '0' }} </td>
                            @if($row['status'] =='1')
                            <td style="text-align: center">
                                <p class="btn btn-success m-1"> Aktif </p>
                            </td>
                            @else
                            <td style="text-align: center">
                                <p class="btn btn-danger m-1"> Tidak Aktif </p>
                            </td>
                            @endif
                            <td>
                                <div class="d-flex justify-content-center">
                                    <a href="{{ route('donate.edit', $row['id']) }}"
                                        class="btn btn-primary m-1">Edit</a>

                                    <button class="btn btn-danger m-1"
                                        onclick="return confirm('Adakah anda pasti ?')">Buang</button>
                                </div>
                            </td>

                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection


@section('script')
<!-- Peity chart-->
<script src="{{ URL::asset('assets/libs/peity/peity.min.js')}}"></script>

<!-- Plugin Js-->
<script src="{{ URL::asset('assets/libs/chartist/chartist.min.js')}}"></script>

<script src="{{ URL::asset('assets/js/pages/dashboard.init.js')}}"></script>

<script>
    function filter() {  
        // alert("Welcome to the javaTpoint.com");  

            // alert($("#organization option:selected").val());
            var organizationid      = $("#organization option:selected").val();
            var _token              = $('input[name="_token"]').val();
            // console.log(schoolid);
            $.ajax({
                url:"{{ route('donate.fetchDonation') }}",
                method: "POST",
                data:{ oid:organizationid,
                        _token:_token },
                success:function(result)
                {
                   console.log(result);
                }
            })
    } 
    // $(document).ready(function(){
        
        

            //     $('#organisasi').change(function(){
        
            //         // $('#kelas').val('');
            //         // $('#murid').val('');
        
            //         if($(this).val() != '')
            //         {
            //             // alert($(this).val();)
            //             // alert($("#sekolah option:selected").val());
            //             var organizationid      = $("#organisasi option:selected").val();
            //             var _token              = $('input[name="_token"]').val();
            //             // console.log(schoolid);
        
            //             $.ajax({
            //                 url:"{{ route('donate.fetchDonation') }}",
            //                 method:"POST",
            //                 data:{ oid:organizationid,
            //                         _token:_token },
            //                 success:function(result)
            //                 {
                               
            //                 }
        
            //             })
        
            //         }
        
        
            //     });

            // });
        
        
</script>
@endsection