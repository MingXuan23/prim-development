@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">
                @auth
                    
                @endauth
            </h4>
        </div>
    </div>
</div>

<div class="container">
<div class="row ">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <img class="card-img-top img-fluid card-img" src="{{ URL::asset('images/derma/utem-derma-1.png') }}"
                                    alt="Image Not Available">
            </div>
        </div>    
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <h4>{{ $donation->nama }} </h4>
                    <form method="POST" action="{{ route('fpxIndex') }}" enctype="multipart/form-data">
                        {{csrf_field()}}
                        <div class="card-body">
                            <div class="form-group">
                                <label>Nama</label>
                                @auth
                                    <input type="text" name="name" class="form-control" placeholder="Nama" value="{{ $user->name }}" required>
                                @endauth
                                @guest
                                    <input type="text" name="name" class="form-control" placeholder="Nama" value="" required>
                                @endguest
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                @auth
                                    <input type="email" parsley-type="email" name="email" class="form-control" placeholder="Email" value="{{ $user->email }}" required>
                                @endauth
                                @guest
                                    <input type="email" parsley-type="email" name="email" class="form-control" placeholder="Email" value="" required>
                                @endguest
                            </div>
                            <div class="form-group">
                                <label>No Telefon</label>
                                @auth
                                    <input type="text" name="telno" class="form-control" data-parsley-pattern="^(\+?6?01)[0|1|2|3|4|6|7|8|9]\-*[0-9]{7,8}$" placeholder="No Telefon" value="{{ $user->telno }}" required>
                                @endauth
                                @guest
                                    <input type="text" name="telno" class="form-control" data-parsley-pattern="^(\+?6?01)[0|1|2|3|4|6|7|8|9]\-*[0-9]{7,8}$" placeholder="No Telefon" value="" required>
                                @endguest
                            </div>
                            <div class="form-group">
                                <label>Amaun</label>
                                <input id="input-currency" class="form-control input-mask text-left"
                                    data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'"
                                    im-insert="true" style="text-align: right;" name="amount" required>
                            </div>

                            <div class="form-group">
                                <label>Pilih Bank</label>
                                <select name="bankid" id="bankid" class="form-control" required>
                                    <option value="">Select bank</option>
                                    <option value="ABB0234">Affin B2C - Test ID</option>
                                    <option value="ABB0233">Affin Bank</option>
                                    <option value="ABMB0212">Alliance Bank (Personal)</option>
                                    <option value="AGRO01">AGRONet</option>
                                    <option value="AMBB0209">AmBank</option>
                                    <option value="BIMB0340">Bank Islam</option>
                                    <option value="BMMB0341">Bank Muamalat</option>
                                    <option value="BKRM0602">Bank Rakyat</option>
                                    <option value="BSN0601">BSN</option>
                                    <option value="BCBB0235">CIMB Clicks</option>
                                    <option value="CIT0219">Citibank</option>
                                    <option value="HLB0224">Hong Leong Bank</option>
                                    <option value="HSBC0223">HSBC Bank</option>
                                    <option value="KFH0346">KFH</option>
                                    <option value="MBB0228">Maybank2E</option>
                                    <option value="MB2U0227">Maybank2U</option>
                                    <option value="OCBC0229">OCBC Bank</option>
                                    <option value="PBB0233">Public Bank</option>
                                    <option value="RHB0218">RHB Bank</option>
                                    <option value="TEST0021">SBI Bank A</option>
                                    <option value="TEST0022">SBI Bank B</option>
                                    <option value="TEST0023">SBI Bank C</option>
                                    <option value="SCB0216">Standard Chartered</option>
                                    <option value="UOB0226">UOB Bank</option>
                                    <option value="UOB0229">UOB Bank - Test ID</option>
                                </select>
                            </div>
                        </div>

                        <input type="hidden" name="desc" id="desc" value="Donation">
                        <input type="hidden" name="o_id" id="o_id" value="{{ $donation->id }} ">

                        <button class="btn btn-success float-right submit" type="submit" onclick="return checkBank();">
                            Bayar Sekarang
                        </button>
                    </form>
            </div>
        </div>    
    </div>
</div>
</div>
{{-- <div id="redirectModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <h4>You will be redirected to secure payment site. Do not close the window until the transaction complete.</h4>
            </div>
        </div>
    </div>
</div> --}}

@endsection


@section('script')
<!-- Peity chart-->
<script src="{{ URL::asset('assets/libs/peity/peity.min.js')}}"></script>

<!-- Plugin Js-->
<script src="{{ URL::asset('assets/libs/chartist/chartist.min.js')}}"></script>

<script src="{{ URL::asset('assets/js/pages/dashboard.init.js')}}"></script>

<script src="{{ URL::asset('assets/libs/inputmask/inputmask.min.js')}}"></script>

<script src="{{ URL::asset('assets/libs/parsleyjs/parsleyjs.min.js')}}"></script>

<script>
    $(document).ready(function () {
        $(".input-mask").inputmask();
    });

    function checkBank() {
        var t = jQuery('#bankid').val();
        if (t === '' || t === null) {
            alert('Please select a bank');
            return false;
        }
    }

    // $(document).on('click', '.submit', function() {
    //     $('#redirectModal').modal('show');
    //     setTimeout("$('#redirectModal').modal('hide');", 3000);
    // });

    $.ajax({
        type: 'GET',
        dataType: 'json',
        url: "/fpx/getBankList",
        success: function(data) {
            for(var i = 0; i < data.data.length; i++){
                // console.log(data.data[i].code);
            }
        },
        error: function (data) {
            // console.log(data);
        }
    });
</script>
@endsection