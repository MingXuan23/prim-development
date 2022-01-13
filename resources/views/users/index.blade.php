@extends('layouts.master')
@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/css/profile1.css') }}" rel="stylesheet" type="text/css" />
<!-- for input mask -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.4.0/css/bootstrap.min.css"/>  

@include('layouts.datatable')

@endsection

@section('content')

<!-- begin title of the page -->
<div class="col-sm-6">
    <div class="page-title-box">
        <h4 class="font-size-18">My Profile</h4>
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
<div class="col-lg-8">
    <div class="card">
        <div class="card-body">
            <!-- name -->
            <div class="col-sm-5">
                <h5 class="mb-0">Name</h5>
            </div>
            <div class="col-sm-9 text-secondary">
                <input type="text" class="form-control" value="{{ $userData->name }} " readonly>
            </div>
            <!-- email -->
            <div class="col-sm-5">
                <h5 class="mb-0">Email address</h5>
            </div>
            <div class="col-sm-9 text-secondary">
                <input type="text" class="form-control" value="{{ $userData->email }}" readonly>
            </div>
            <!-- username -->
            <div class="col-sm-5">
                <h5 class="mb-0">Username</h6>
            </div>
            <div class="col-sm-9 text-secondary">
                <input type="text" class="form-control" value="{{ $userData->username }}" readonly>
            </div>
            <!-- phone number -->
            <div class="col-sm-5">
                <h5 class="mb-0">Phone number</h6>
            </div>
            <div class="col-sm-9 text-secondary">
                <input type="text" class="form-control phone_no" value="{{ $userData->telno }}" readonly>
            </div>

            <!-- Address -->
            <div class="col-sm-5">
                <h5 class="mb-0">Address</h6>
            </div>
            <div class="col-sm-9 text-secondary">
                <input type="text" class="form-control" value="{{ $userData->address }}" readonly>
            </div>

            <!-- Postcode -->
            <div class="col-sm-5">
                <h5 class="mb-0">Postcode</h6>
            </div>
            <div class="col-sm-9 text-secondary">
                <input type="text" class="form-control" value="{{ $userData->postcode }}" readonly>
            </div>

            <!-- State -->
            <div class="col-sm-5">
                <h5 class="mb-0">State</h6>
            </div>
            <div class="col-sm-9 text-secondary dataState">
                <input type="text" class="form-control" value="{{ $userData->state }}" readonly>
            </div>

        </div> <!-- end of card-body -->

        <!-- button for edit -->
        <div class="btn-group editBtnGrp" role="group" aria-label="">
        <!-- <a class="btn btn-light w-md waves-effect waves-light border border-dark" href="">Reset password</a> -->
            <a class="btn btn-light w-md waves-effect waves-light border border-dark" href="{{ route('profile_change_password') }}">Reset password</a>
            <a class="btn btn-light w-md waves-effect waves-light border border-dark" href="{{ route('profile_edit') }}">
                Edit Details
            </a>

            <!-- <a class="btn" href="{{ route('profile_edit') }}">
                <img class="edit_icon" src="{{ URL::to('/assets/images/users/edit_icon.png') }}">
            </a> -->
        </div>
           
    </div> <!-- end of card -->
</div> <!-- end of most outer -->
@endsection
@section('script')
<script src="{{ URL::asset('assets/libs/jquery-mask/jquery.mask.min.js')}}"></script>
<script>
    $(document).ready(function () {
        $('.phone_no').mask('+600000000000');
    });
</script>
@endsection


