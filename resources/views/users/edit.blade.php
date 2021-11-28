@extends('users.profile_layout');

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

<form action="profile_update" method="post">
@csrf 
    <table class="form-group">
        <tr>
            <td> <label>Name:</label> </td>
            <td> <input type="text" class="form-control" value="{{ Auth::user()->name }}" name="name"></td>
        </tr>
        <tr>
            <td> <label>Email:</label> </td>
            <td> <input type="email" class="form-control" value="{{ Auth::user()->email }}" name="email"></td>
        </tr>
        <tr>
            <td><label>Username:</label></td>
            <td><input type="text" class="form-control" value="{{ Auth::user()->username }}" name="username"></td>
        </tr>
        <tr>
            <td><label>Tel No:</label></td>
            <td><input type="text" class="form-control" value="{{ Auth::user()->telno }}" name="telno"></td>
        </tr>
        <tr>
            <td><label>Address:</label></td>
            <td><input type="text" class="form-control" value="{{ Auth::user()->address }}" name="address"></td>
        </tr>
        <tr>
            <td><label>Postcode:</label></td>
            <td><input type="text" class="form-control" value="{{ Auth::user()->postcode }}" name="postcode"></td>
        </tr>
        <tr>
            <td><label>State:</label></td>
            <td><input type="text" class="form-control" value="{{ Auth::user()->state }}" name="state"></td>
        </tr>
    </table>
    <button type="submit" class="btn btn-light w-md waves-effect waves-light" name="submit_btn">Update</button>
    <button type="button" class="btn btn-light w-md waves-effect waves-light" onclick="window.location='{{ url("/profile_user") }}'">Back</button>
    <!-- <a class="btn" href="profile_user">Back</a> -->
</form>
@endsection
