@extends('layouts.master')

@section('css')
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
</style>

@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">

            </h4>
        </div>
    </div>
</div>

<div class="container">
    <div class="row ">
        <div class="col-lg-6 ">
            <div class="card h-100">
                <div class="card-body">
                    <img class="img-fluid card-img"
                        src="{{ URL::asset('/donation-poster/'.$donation->donation_poster) }}"
                        alt="Image Not Available">
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-body">
                    <h4 class="card-title" style="text-align: center; font-size: 18px">{{ $donation->nama }} </h4>
                    <br>
                    <form class="form-validation" method="POST" action="{{ route('fpxIndex') }}"
                        enctype="multipart/form-data">
                        {{csrf_field()}}
                        <div class="form-group">
                            <label>Nama</label>
                            <input type="text" name="name" id="name" class="form-control" placeholder="Nama"
                                value="{{ !empty(auth()->user()->id) ? $user->name : '' }}"
                                data-parsley-required-message="Sila masukkan nama penuh" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" parsley-type="email" name="email" class="form-control"
                                placeholder="Email" value="{{ !empty(auth()->user()->id) ? $user->email : '' }}"
                                data-parsley-required-message="Sila masukkan email" required>
                        </div>
                        <div class="form-group">
                            <label>No Telefon</label>
                            <input type="text" name="telno" class="form-control phone_no" placeholder="No Telefon"
                                value="{{ !empty(auth()->user()->id) ? $user->telno : '' }}"
                                data-parsley-required-message="Sila masukkan no telefon" required>
                        </div>
                        <div class="form-group">
                            <label>Amaun</label>
                            <input id="input-currency" class="form-control input-mask text-left"
                                data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'"
                                im-insert="true" style="text-align: right;" name="amount" data-parsley-min="2"
                                data-parsley-required-message="Sila masukkan amaun"
                                data-parsley-error-message="Minimum jumlah untuk diderma adalah RM2.00" required>
                                <p><i>*Minimum RM 2</i> </p>
                        </div>
                        <div class="form-group">
                            <label>Pilih Bank</label>
                            <select name="bankid" id="bankid" class="form-control"
                                data-parsley-required-message="Sila pilih bank" required>
                                <option value="">Select bank</option>
                            </select>
                        </div>

                        <input type="hidden" name="desc" id="desc" value="Donation">
                        <input type="hidden" name="o_id" id="o_id" value="{{ $donation->id }} ">
                        
                        <button class="btn btn-success float-right submit" type="submit">
                            Teruskan
                        </button>
                        <br><br>
                        <div style="text-align: center;">
                            <p><i>"<b>ALLAH</b> suka kita <b>SEDEKAH</b> setiap hari"</i></p>
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

@endsection


@section('script')
<script src="{{ URL::asset('assets/libs/inputmask/inputmask.min.js')}}"></script>
<script src="{{ URL::asset('assets/libs/jquery-mask/jquery.mask.min.js')}}"></script>
<script src="{{ URL::asset('assets/libs/parsleyjs/parsleyjs.min.js')}}"></script>

<script>
    $(document).ready(function () {
        $(".input-mask").inputmask();
        $('.phone_no').mask('+600000000000');
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