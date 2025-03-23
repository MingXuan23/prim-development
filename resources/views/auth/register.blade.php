@extends('layouts.master-without-nav')

@section('title')
Register
@endsection

@section('body')

<body>

    <style>
        /* #name {
            text-transform: uppercase;
        } */

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

<div class="account-pages my-5 pt-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6 col-xl-5">
                <div class="card overflow-hidden">
                    <div class="bg-primary">
                        <div class="text-primary text-center p-4">
                            <h5 class="text-white font-size-20">Daftar</h5>
                            <p class="text-white-50">Ayuh mendaftar diri anda bersama PRIM</p>
                            <a href="/derma" class="logo logo-admin">
                                <img src="assets/images/logo/prim-logo2.svg" height="60" alt="logo">
                            </a>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <div class="p-3">

                            <form method="POST" class="form-horizontal mt-4" action="{{ route('register') }}">
                                @csrf
                                <div class="form-group">
                                    <input type="hidden" class="form-control @error('password') is-invalid @enderror" 
                                        name="referral_code" id="referral_code">
                                    @error('referral_code')
                                    <div class="invalid-feedback" style="display: block;">
                                        <strong>{{ $message }}</strong>
                                    </div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="registration_type">Pilih Jenis Pendaftaran</label>
                                    <select name="registration_type" id="registration_type" class="form-control @error('registration_type') is-invalid @enderror">
                                        <option value="-">-- Sila Pilih --</option>
                                        <option value="bayar_yuran">Bayar Yuran</option>
                                        <option value="beli_barang">Beli Barang (Get &amp; Go)</option>
                                        <option value="sewa_homestay">Sewa Homestay</option>
                                        <option value="ganjaran_derma_prim">Ganjaran Derma PRiM</option>
                                    </select>
                                    @error('registration_type')
                                    
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                </div>
                               

                                <div class="form-group">
                                    <label for="name">Nama Penuh</label>
                                    <input type="text" name="name" id="name"
                                        class="form-control @error('name') is-invalid @enderror" required
                                        placeholder="Nama Penuh">
                                    @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="useremail">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        value="{{old('email')}}" id="useremail" name="email" required
                                        placeholder="Email">
                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="telno">No. Telefon</label>
                                    <input type="text" name="telno" id="telno"
                                        class="form-control phone_no @error('telno') is-invalid @enderror"
                                        placeholder="Nombor Telefon" max="11">
                                    @error('telno') 
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>

                                <!-- New combobox for registration type -->
                               

                                <div class="form-group">
                                    <label for="userpassword">Kata Laluan</label>
                                    <input type="password"
                                        class="form-control @error('password') is-invalid @enderror" name="password"
                                        required id="userpassword" placeholder="Kata Laluan">
                                    @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="password-confirm">Pengesahan Kata Laluan</label>
                                    <input id="password-confirm" type="password" name="password_confirmation"
                                        class="form-control @error('password_confirmation') is-invalid @enderror"
                                        required placeholder="Sahkan Kata Laluan">
                                </div>

                                <div class="form-group row">
                                    <div class="col-12 text-center">
                                        <button class="btn btn-light w-md waves-effect waves-light"
                                            type="button" onclick="window.location='{{ url("/") }}'">Kembali</button>

                                        <button class="btn btn-primary w-md waves-effect waves-light"
                                            type="submit">Daftar</button>
                                    </div>
                                </div>

                                <div class="form-group mt-2 mb-0 row">
                                    <div class="col-12 mt-4 text-center">
                                        <p class="mb-0">Dengan mengklik Daftar, anda bersetuju dengan <a href="#"
                                                style="pointer-events: none;" class="text-primary">Terma</a> PRIM</p>
                                    </div>
                                </div>
                                
                            </form>

                        </div>
                    </div>

                </div>

                <div class="mt-5 text-center">
                    <p>Sudah berdaftar bersama PRIM? <a href="/login" class="font-weight-medium text-primary"> Log Masuk
                        </a> </p>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="{{ URL::asset('assets/libs/jquery-mask/jquery.mask.min.js')}}"></script>

<script>
    $(document).ready(function () {
        $('.phone_no').mask('+600000000000');

        // Function to update the input field with the referral code
        function updateReferralCode() {
            const urlParams = new URLSearchParams(window.location.search);
            const referralCode = urlParams.get('referral_code');
            if (referralCode) {
                document.getElementById('referral_code').value = referralCode;
            }
        }

        updateReferralCode();
    });
</script>
@endsection
