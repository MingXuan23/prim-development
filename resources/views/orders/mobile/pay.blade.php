@extends('layouts.master')

@section('css')

<link rel="stylesheet" href="{{ URL::asset('assets/css/datatable.css')}}">
@include('layouts.datatable')

<style>
:root {
    --primary-bc: #ffffff;
    --secondary-bc: rgb(2, 122, 129);
    --hover-color:rgb(6, 225, 237);
    --primary-color:#5b626b;
    --transition: all 0.3s linear;
}
.main-content{
    color: var(--primary-color);
}
.noborder{
  border: none!important;
}

#img-size {
  width: 100px;
  height: 100px;
  object-fit: cover;
}

.loading {
  width: 35px;
  height: 35px;
  display: none;
}

/* Loading Modal Styles */
#loadingModal {
  display: none; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 999; /* Sit on top */
  padding-top: 100px; /* Location of the box */
  left: 0;
  top: 0;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: rgb(0,0,0); /* Fallback color */
  background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

#loadingModal .modal-content {
  background-color: #fefefe;
  margin: auto;
  padding: 20px;
  border: 1px solid #888;
  width: 80%;
  text-align: center;
  border-radius: 15px; /* Adjust the value as needed */
}

#loadingModal .spinner {
  margin: 20px auto;
  border: 8px solid #f3f3f3;
  border-radius: 50%;
  border-top: 8px solid var(--primary-color);
  width: 60px;
  height: 60px;
  -webkit-animation: spin 2s linear infinite; /* Safari */
  animation: spin 2s linear infinite;
}

@-webkit-keyframes spin {
  0% { -webkit-transform: rotate(0deg); }
  100% { -webkit-transform: rotate(360deg); }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>

@endsection

@section('content')

<!-- Loading Modal -->
<div id="loadingModal">
  <div class="modal-content">
    <div class="spinner"></div>
  </div>
</div>

<div class="container">
  <div class="row d-flex justify-content-center align-items-center">
    <div class="col">
      <div class="d-flex justify-content-center align-items-center">
        <span class="h2 m-4">Pembayaran</span>
      </div>

      <div class="card">
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

          <form id="paymentForm" action="{{ route('directpayIndex') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <input type="hidden" name="desc" id="desc" value="OrderS">
            <input type="hidden" name="user_id" id="user_id" value="{{ $users->id }}">
            <input type="hidden" name="name" id="name" value="{{ $users->name }}">
            <input type="hidden" name="email" id="email" value="{{ $users->email }}">
            <input type="hidden" name="telno" id="telno" value="{{ $users->telno }}">
            <input type="hidden" name="amount" id="amount" value="{{ $totalamount }}">
            <input type="hidden" name="order_cart_id" id="order_cart_id" value="{{ $order_cart->id }}">
            <input type="hidden" name="organ_id" id="organ_id" value="{{ $organizations->id }}">
            <input type="hidden" name="mobile" id="mobile" value=true>
          </form>

        </div>
      </div>
    </div>
  </div>
</div>

@endsection

@section('script')

<script>
document.addEventListener("DOMContentLoaded", function() {
  // Show the loading modal
  var loadingModal = document.getElementById("loadingModal");
  loadingModal.style.display = "block";

  // Submit the form
  document.getElementById("paymentForm").submit();
});
</script>

@endsection
