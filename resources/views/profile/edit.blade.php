@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
@include('layouts.datatable')

@endsection

@section('content')
<!-- begin title of the page -->
<div class="col-sm-6">
    <div class="page-title-box">
        <h4 class="font-size-18">Edit Profil</h4>
    </div>
</div>
<!-- end of title of the page -->

@if($message = Session::get('success'))
 <div class="alert alert-success"> <!-- update message -->
     <p>{{ $message }}</p>
</div>
@endif


<!-- error message -->
<div class="card ">
    <div class="card-body p-4">
        <form action="{{ route("profile.update", Auth::id()) }}" class="form-horizontal" method="post">
            @method('PATCH')
            {{csrf_field()}}
            <div class="form-group"><!-- name  -->
                <label for="name">Nama penuh:</label>
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
                <label for="useremail">Emel:</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror"
                value="@error('email'){{ old('email')}}  @enderror  @if (!(old('email'))) {{ Auth::user()->email }} @endif" 
                name="email">
                <!-- value=" @error('email'){{ old('email')}}  @enderror  @if (!(old('email'))) {{-- Auth::user()->email --}} @endif"  -->
                <!-- if got error then take back the old email, if no old -->

                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div> <!-- end of email -->

            <div class="form-group">
                <!-- username -->
                <label for="username">Nama pengguna:</label>
                <input type="text" class="form-control @error('username') is-invalid @enderror" value="{{ Auth::user()->username }}" name="username">
                @error('username')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div> <!-- end of username -->

            <div class="form-group">
                <label>Nombor Kad Pengenalan</label>
                <input type="text" name="icno" class="form-control icno" placeholder="Nombor Kad Pengenalan"
                    value="{{ Auth::user()->icno}}">
            </div>

            <div class="form-group">
                <!-- telno -->
                <label for="telno">No. Telefon:</label>
                <input type="text" name="telno"  
                class="form-control  phone_no  @error('telno') is-invalid @enderror" 
                value="@error('telno'){{ old('telno')}}  @enderror  @if (!(old('telno'))) {{ $usertel }} @endif" 
                data-parsley-required-message="Sila masukkan no telefon"
                min="10"  max="13" 
                >

                <!-- <input type="text" name="telno" class="form-control phone_no" placeholder="No Telefon"
                 value="{{-- $org->telno --}}" data-parsley-required-message="Sila masukkan no telefon" required> -->

                @error('telno')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>  
                    </span>
                @enderror
            </div> <!-- end of telno -->
            <!-- phone_no  -->

            <div class="form-group">
            <!-- address -->
            <label for="address">Alamat:</label>
            <input type="text" class="form-control" value="{{ Auth::user()->address }}" name="address">
            @error('address')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
            @enderror        
        </div> <!-- end of address --> 
                    
            <div class="form-group">
            <label for="postcode">Poskod:</label>
            <input type="text" class="form-control postcode"
            value="{{ Auth::user()->postcode }}" name="postcode"
            >
           

           <!--  pattern="[0-9]{5}"
             -->

            @error('postcode')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div> <!-- end of postsode -->
            
        <div class="form-group">
            <!-- state -->
            <label for="state">Negeri: </label>
                <div class= "dataState">

                    <select name="state" id="state" class="form-control ">
                        <option value="{{ Auth::user()->state }}">Pilih Negeri</option>
                            @for ($i = 0; $i < count($states); $i++)
                                @if(ucfirst(strtolower($states[$i]['name'])) == Auth::user()->state)
                                    <option id="{{ $states[$i]['id'] }}" value="{{ Auth::user()->state }}" selected> {{ Auth::user()->state }} </option>
                                @else
                                    <option id="{{ $states[$i]['id'] }}" value="{{ ucfirst(strtolower($states[$i]['name'])) }}">{{ ucfirst(strtolower($states[$i]['name'])) }}</option>
                                @endif
                                @endfor
                </select>       

                </div>
            
            @error('state')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
             @enderror
        </div> <!-- end of state -->
            
            <div class="form-group row">
                <div class="col-12 text-right">
                <button type="button" class="btn btn-light w-md waves-effect waves-light" onclick="window.location='{{ route("profile.index") }}'">Back</button>
                    <button type="submit" class="btn btn-primary w-md waves-effect waves-light" name="submit_btn">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>
<!-- end of card body -->

@endsection
@section('script')
<script src="{{ URL::asset('assets/libs/parsleyjs/parsleyjs.min.js')}}"></script>
<script src="{{ URL::asset('assets/libs/inputmask/inputmask.min.js')}}"></script>
<script src="{{ URL::asset('assets/libs/jquery-mask/jquery.mask.min.js')}}"></script>

<script>
        
        $(document).ready(function () {
        $('.form-validation').parsley();
        $('.phone_no').mask('+600000000000');
        $('.postcode').mask('99999');
        $('.icno').mask('000000000000');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        function toTitleCase(str) {
            var lcStr = str.toLowerCase();
            return lcStr.replace(/(?:^|\s)\w/g, function(match) {
                return match.toUpperCase();
            });
        }
    });

</script>
@endsection