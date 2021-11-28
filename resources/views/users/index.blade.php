@extends('users.profile_layout');

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/css/user-profile.css')}}" rel="stylesheet" type="text/css" />
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
<!-- button for edit -->
<div class="sticky">
    <a class="btn" href="{{ route('profile_edit') }}">
        <img src="{{ URL::to('/assets/images/users/edit_icon.png') }}">
    </a>
</div>
<!-- display data -->
<div class="profile-box">
    <label class="title-label">Name:  </label> {{ $userData->name }} 
</div> 
<div class="profile-box">
    <label class="title-label">Email address: </label> {{ $userData->email }}
</div> 
<div class="profile-box">
    <label class="title-label">Username: </label> {{ $userData->username }}
</div>  
<div class="profile-box">
    <label class="title-label">Phone number: </label> {{ $userData->telno }}
</div>
<div class="profile-box">
    <label class="title-label">Address: </label> {{ $userData->address }}
</div>
<div class="profile-box">
    <label class="title-label">Postcode: </label>{{ $userData->postcode }}
</div> 
<div class="profile-box">
    <label class="title-label">State: </label>{{ $userData->state }} 
</div>



@endsection


