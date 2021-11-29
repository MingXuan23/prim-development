@extends('users.profile_layout')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
@include('layouts.datatable')

@endsection

@section('content')
<!-- begin title of the page -->
<div class="col-sm-6">
    <div class="page-title-box">
        <h4 class="font-size-18">Edit Profile</h4>
    </div>
</div>
<!-- end of title of the page -->

<!-- error message -->
<div class="card">
    <div class="card-body p-4" style="width: 40rem;">
        <form action="profile_update" class=" form-horizontal" method="post">
            @csrf 
            <div class="form-group"><!-- name  -->
                <label for="name">Name:</label>
                <input type="text" name="name" id="name"  class="form-control @error('name') is-invalid @enderror" required
                value="{{ Auth::user()->name }}">
                @error('name')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>  <!-- end of name -->

            <div class="form-group">
                <!-- email -->
                <label for="useremail">Email</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror"
                value="{{ Auth::user()->email }}" name="email">
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div> <!-- end of email -->

            <div class="form-group">
                <!-- username -->
                <label for="username">Username:</label>
                <input type="text" class="form-control @error('username') is-invalid @enderror" value="{{ Auth::user()->username }}" name="username">
                @error('username')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div> <!-- end of username -->

            <div class="form-group">
                <!-- telno -->
                <label for="telno">Tel No:</label>
                <input type="text" name="telno"  
                class="form-control phone_no @error('telno') is-invalid @enderror" value="{{ Auth::user()->telno }}" name="username">
                @error('telno')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                        
                    </span>
                @enderror
            </div> <!-- end of telno -->

            <div class="form-group">
            <!-- address -->
            <label for="address">Address:</label>
            <input type="text" class="form-control" value="{{ Auth::user()->address }}" name="address">
            @error('address')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror        
        </div> <!-- end of address --> 
                    
            <div class="form-group">
            <label for="postcode">Postcode:</label>
            <input type="text" class="form-control" value="{{ Auth::user()->postcode }}" name="postcode">
            @error('postcode')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div> <!-- end of postsode -->
            
        <div class="form-group">
            <!-- state -->
            <label for="state">State:</label>
                <!-- maybe can develop into option -->
            <input type="text" class="form-control" value="{{ Auth::user()->state }}" name="state">
            @error('state')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
             @enderror
        </div> <!-- end of state -->
            
            <div class="form-group row">
                <div class="col-12 text-center">
                    <button type="submit" class="btn btn-light w-md waves-effect waves-light" name="submit_btn">Update</button>
                    <button type="button" class="btn btn-light w-md waves-effect waves-light" onclick="window.location='{{ url("/profile_user") }}'">Back</button>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- end of card body -->

@endsection
