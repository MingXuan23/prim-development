@extends('layouts.master')
@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css"/>
<link href="{{ URL::asset('assets/css/profile.css') }}" rel="stylesheet" type="text/css"/>
<!-- for input mask -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.4.0/css/bootstrap.min.css"/>  

@include('layouts.datatable')

@endsection

@section('content')

<!-- begin title of the page -->
<div class="col-sm-6">
    <div class="page-title-box">
        <h4 class="font-size-18">Profil Saya</h4>
    </div>
</div>
<!-- end of title of the page -->
<hr>
@if($message = Session::get('success'))
 <div class="alert alert-success"> <!-- update message -->
     <p>{{ $message }}</p>
</div>
@endif

<!-- display data -->
<div class="col-lg-12">
    <div class="card">
        <div class="card-body">
            <!-- name -->
            <div class="col-sm-5">
                <h5 class="mb-0">Nama Penuh</h5>
            </div>
            <div class="col-sm-12 text-secondary">
                <input type="text" class="form-control" value="{{ $userData->name }} " readonly>
            </div>
            <!-- email -->
            <div class="col-sm-5">
                <h5 class="mb-0">Emel</h5>
            </div>
            <div class="col-sm-12 text-secondary">
                <input type="text" class="form-control" value="{{ $userData->email }}" readonly>
            </div>
            <!-- username -->
            <div class="col-sm-5">
                <h5 class="mb-0">Nama pengguna</h6>
            </div>
            <div class="col-sm-12 text-secondary">
                <input type="text" class="form-control" value="{{ $userData->username }}" readonly>
            </div>
            <!-- phone number -->
            <div class="col-sm-5">
                <h5 class="mb-0">No. Telefon</h6>
            </div>
            <div class="col-sm-12 text-secondary">
                <input type="text" class="form-control phone_no" value="{{ $userData->telno }}" readonly>
            </div>

            <!-- Address -->
            <div class="col-sm-5">
                <h5 class="mb-0">Alamat</h6>
            </div>
            <div class="col-sm-12 text-secondary">
                <input type="text" class="form-control" value="{{ $userData->address }}" readonly>
            </div>

            <!-- Postcode -->
            <div class="col-sm-5">
                <h5 class="mb-0">Poskod</h6>
            </div>
            <div class="col-sm-12 text-secondary">
                <input type="text" class="form-control" value="{{ $userData->postcode }}" readonly>
            </div>

            <!-- State -->
            <div class="col-sm-5">
                <h5 class="mb-0">Negeri</h6>
            </div>
            <div class="col-sm-12 text-secondary dataState">
                <input type="text" class="form-control" value="{{ $userData->state }}" readonly>
            </div>

            <div class="col-sm-5">
                <h5 class="mb-0">Mata Ganjaran PRiM</h6>
            </div>
            @if($referral_code !=null)
            <div class="col-sm-12 text-secondary dataState">
                <input type="text" class="form-control" value="{{$referral_code->total_point}} Mata Ganjaran" readonly>
            </div>
            @else
            <div class="col-sm-12 text-secondary dataState">
            <button class="btn btn-primary w-md waves-effect waves-light" onclick="copyReferralLink()">
                   Aktifkan
                </button>
            </div>

            @endif
            <!-- button for edit -->
            <div class="btn-group editBtnGrp" role="group" aria-label="">
                <a class="btn btn-light w-md waves-effect waves-light" href="{{ route('profile.resetPassword') }}">Tukar Kata Laluan</a>
                <a class="btn btn-primary w-md waves-effect waves-light" href="{{ route('profile.edit', $userData->id) }}">
                    Edit Profil
                </a>
            </div>
        </div> <!-- end of card-body -->
            
    </div> <!-- end of card -->
</div> <!-- end of most outer -->

@endsection
@section('script')
<script src="{{ URL::asset('assets/libs/jquery-mask/jquery.mask.min.js')}}"></script>
<script>
    $(document).ready(function () {
        $('.phone_no').mask('+600000000000');
    });

    function copyReferralLink(){
        $.ajax({
        method: 'GET',
        url: "{{route('donate.getReferralCode')}}",
        success: function(data) {
          
            window.location.reload();
        },

    });
    }
</script>
@endsection


