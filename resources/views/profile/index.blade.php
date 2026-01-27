@extends('layouts.master')
@section('css')
    <link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/css/profile.css') }}" rel="stylesheet" type="text/css" />
    <!-- for input mask -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.4.0/css/bootstrap.min.css" />

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

                <div class="col-sm-9">

                    <div class="col-sm-6">
                        <h5 class="mb-0">Mata Ganjaran PRiM</h6>

                    </div>
                    <div class="row mb-12 ">
                        @if($referral_code != null)
                            <div class="col-sm-6 text-secondary ">
                                <input type="text" class="form-control" value="{{$referral_code->total_point}} Mata Ganjaran"
                                    readonly>
                            </div>
                            <div class="col-sm-3 text-secondary ">
                                <a class="btn btn-primary w-md waves-effect waves-light form-control mb-3"
                                    href="{{ route('point.index') }}">Butiran</a>

                            </div>
                            <div class="col-sm-3 text-secondary ">
                                <a class="btn btn-success w-md waves-effect waves-light form-control mb-3" href="#"
                                    data-toggle="modal" data-target="#itemModal">Dapat Link</a>

                            </div>


                        @else

                            <button class="btn btn-primary waves-effect waves-light" style="margin-left:22px"
                                onclick="copyReferralLink()">
                                Aktifkan
                            </button>

                        @endif
                    </div>

                </div>
                <div class="alert alert-success" style="display:none;">
                    <p id="success"></p>
                </div>

                <br>
                <br>
                <br>
                <!-- button for edit -->
                <!-- <div class="btn-group editBtnGrp" role="group" aria-label="">
                        <a class="btn btn-light w-md waves-effect waves-light" href="{{ route('profile.resetPassword') }}">Tukar
                            Kata Laluan</a>
                        <a class="btn btn-primary w-md waves-effect waves-light"
                            href="{{ route('profile.edit', $userData->id) }}">
                            Edit Profil
                        </a>
                    </div> -->
            </div> <!-- end of card-body -->

        </div> <!-- end of card -->
    </div> <!-- end of most outer -->


    <!-- Modal -->
    <div class="modal fade" id="itemModal" tabindex="-1" role="dialog" aria-labelledby="itemModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="itemModalLabel">Pilih Link</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <select id="itemSelect" class="form-control">
                        <!-- Populate options dynamically using JavaScript or Blade -->
                        <option value="R" selected>Inviatation Link</option>
                        <option value="M">Get & Go</option>

                        <!-- Add more options as needed -->
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="sendRequest()" data-dismiss="modal">Copy</button>
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
            //  $('.alert').delay(5000).fadeOut();
        });

        function copyReferralLink() {
            $.ajax({
                method: 'GET',
                url: "{{route('point.getReferralCode')}}",
                success: function (data) {

                    window.location.reload();
                },

            });


        }

        function sendRequest() {
            var selectedItem = document.getElementById('itemSelect').value;
            $.ajax({
                method: 'GET',
                data: {
                    page: selectedItem
                },
                url: "{{route('point.shareReferralLink')}}",
                success: function (data) {

                    copyToClipboard(data.link, true)
                    console.log(data);
                },

            });
        }

        function copyToClipboard(text, isReferralCode) {
            // Create a temporary input element
            var input = document.createElement('input');

            // Set its value to the text that needs to be copied
            input.style.position = 'absolute';
            input.style.left = '-9999px';
            input.value = text;


            // Append it to the body
            document.body.appendChild(input);

            // Select the text inside the input element
            input.select();

            // Execute the copy command
            document.execCommand('copy');

            // Remove the input element from the DOM
            document.body.removeChild(input);
            if (isReferralCode) {
                $('#success').text('Url with your referral code copied');
            } else {
                $('#success').text('Url copied, login to get the referral code');

            }

            $('.alert-success').show();
            setTimeout(function () {
                $('.alert').css('display', 'none');
            }, 7500);

            // Set a timeout to hide the alert after 3 seconds


        }

    </script>
@endsection