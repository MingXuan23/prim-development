@extends('layouts.master')

@section('css')

<link rel="stylesheet" href="">
<link href="{{ URL::asset('assets/css/normalize.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
<style>
    #name {
        text-transform: uppercase;
    }

    ::-webkit-input-placeholder {
        /* WebKit browsers */
        text-transform: none;
    }

    :-moz-placeholder {
        /* Mozilla Firefox 4 to 18 */
        text-transform: none;
    }

    ::-moz-placeholder {
        /* Mozilla Firefox 19+ */
        text-transform: none;
    }

    :-ms-input-placeholder {
        /* Internet Explorer 10+ */
        text-transform: none;
    }

    ::placeholder {
        /* Recent browsers */
        text-transform: none;
    }

    @media only screen and (max-width: 800px) {

        #bg-ballon {
            visibility: hidden;
            clear: both;
            float: left;
            margin: 10px auto 5px 20px;
            width: 28%;
            display: none;
            background-color: #500ade
        }

        body {
            overflow-y: scroll !important;
        }


    }

    .boxed-btn {
        display: inline-block;
        text-align: center;
        line-height: 60px;
        font-size: 16px;
        text-transform: capitalize;
        font-weight: 500;
        color: #fff;
        background-color: #500ade;
        padding: 0 20px;
        -webkit-transition: all .3s ease-in;
        -moz-transition: all .3s ease-in;
        -o-transition: all .3s ease-in;
        transition: all .3s ease-in;
    }

    .boxed-btn:hover {
        color: #fff;
        background-color: #333;
    }

    .boxed-btn.btn-rounded {
        border-radius: 30px;
    }

    a,
    a:active,
    a:focus,
    a:hover,
    button {
        text-decoration: none;
        outline: 0;
        font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
        border: transparent;
    }

    .btn-donation {
        color: white;
        width: 100%;
        height: 45px;
        line-height: 45px;
    }

    .form-control {
        border-radius: 0.75rem !important;
    }

    .bg-form {
        background-image: url("{{ URL::asset('assets/landing-page/img/bg/team-shape.png') }}");
        background-repeat: no-repeat;
        background-position: center;
    }

    body::-webkit-scrollbar {
        display: none;
    }

    /* Hide scrollbar for IE, Edge and Firefox */
    body {
        -ms-overflow-style: none;
        /* IE and Edge */
        scrollbar-width: none;
        /* Firefox */
    }

    select {
        text-align: -moz-left !important;
        text-align: -webkit-left !important;
    }
</style>

@endsection

@section('body')
<img id="bg-ballon" src="{{ URL::asset('assets/landing-page/img/bg/team-shape.png') }}" alt="" style="position:
    absolute">
@endsection

@section('content')
<div class="bg-shape-1">
    <div class="container border rounded p-3 mb-2 card">
        <div class="row bg-form">
            <div class=" col-lg-6">
                <div class="h-100">
                    <div class="card-body">
                        <img class="img-fluid card-img"
                            src="{{ URL::asset('/donation-poster/'.$donation->donation_poster) }}"
                            alt="Image Not Available">
                        {{-- <img class="img-fluid card-img"
                            src="{{ URL::asset('/donation-poster/donation-poster-1623917363.jpg') }}"
                        alt="Image Not Available"> --}}
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class=" h-100">
                    <div class="card-body">
                        <h4 class="card-title" style="text-align: center; font-size: 18px">{{ $donation->nama }} </h4>
                        <br>
                        <form id="bayarform" class="form-validation" method="POST" action="{{ route('fpxIndex') }}"
                            enctype="multipart/form-data">
                            {{csrf_field()}}
                            <div class="form-group">
                                <label>Nama</label>
                                <input type="text" name="name" id="name" class="form-control text-left"
                                    placeholder="Nama" value="{{ !empty(auth()->user()->id) ? $user->name : '' }}"
                                    data-parsley-required-message="Sila masukkan nama penuh" required>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" parsley-type="email" name="email" class="form-control text-left"
                                    placeholder="Email" value="{{ !empty(auth()->user()->id) ? $user->email : '' }}"
                                    data-parsley-required-message="Sila masukkan email" required>
                            </div>
                            <div class="form-group">
                                <label>No Telefon</label>
                                <input type="text" name="telno" class="form-control phone_no text-left"
                                    placeholder="No Telefon"
                                    value="{{ !empty(auth()->user()->id) ?  substr($user->telno , 2) : '' }}"
                                    data-parsley-required-message="Sila masukkan no telefon" required>
                            </div>
                            <div class="form-group">
                                <label>Amaun</label>
                                <input id="input-currency" class="form-control input-mask text-left"
                                    data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'"
                                    im-insert="true" name="amount" data-parsley-min="2"
                                    data-parsley-required-message="Sila masukkan amaun"
                                    data-parsley-error-message="Minimum jumlah untuk diderma adalah RM2.00" required>
                                <p><i>*Minimum RM 2</i> </p>
                            </div>
                            <div class="form-group">
                                <label>Pilih Bank</label>
                                <select name="bankid" id="bankid" class="form-control"
                                    data-parsley-required-message="Sila pilih bank" required>
                                    <option value="">Pilih bank</option>
                                </select>

                            </div>

                            <input type="hidden" name="desc" id="desc" value="Donation">
                            <input type="hidden" name="o_id" id="o_id" value="{{ $donation->id }} ">
                            <br>

                            <div class="col-sm-12">
                                <button class="boxed-btn btn-rounded btn-donation submit" type="submit">
                                    Derma
                                </button>
                            </div>

                            <br>
                            <div style="text-align: center;">
                                <p><i>"The  <b>POWER</b> of <b>GIVING</b>"</i></p>
                                <img src="{{ URL::asset('assets/images/pic-donate.png') }}" width="150">
                            </div>

                        </form>

                    </div>
                    <div class="row">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection


@section('script')
<script src="{{ URL::asset('assets/libs/inputmask/inputmask.min.js')}}"></script>
<script src="{{ URL::asset('assets/libs/jquery-mask/jquery.mask.min.js')}}"></script>
<script src="{{ URL::asset('assets/libs/parsleyjs/parsleyjs.min.js')}}"></script>

<script>
    $(document).ready(function () {
        $(".input-mask").inputmask();
        $('.phone_no').mask('01000000000');
        $('.form-validation').parsley();


    });

    function checkBank() {
        var t = jQuery('#bankid').val();
        if (t === '' || t === null) {
            alert('Please select a bank');
            return false;
        }
    }
    
    $(document).ready(function() {
	    $('.form-validation').parsley();
    });

    var arr = [];

    $.ajax({
        type: 'GET',
        dataType: 'json',
        url: "/fpx/getBankList",
        success: function(data) {
            jQuery.each(data.data, function(key, value){
                arr.push(key);
            });
            for(var i = 0; i < arr.length; i++){
                arr.sort();
                $("#bankid").append("<option value='"+data.data[arr[i]].code+"'>"+data.data[arr[i]].nama+"</option>");
            }

        },
        error: function (data) {
            // console.log(data);
        }
    });
</script>
@endsection