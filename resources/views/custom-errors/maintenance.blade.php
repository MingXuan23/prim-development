<!doctype html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <title>Maintenance | PRIM</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Parental Relationship Information Management" name="description" />
    <meta content="UTeM" name="author" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ URL::asset('assets/images/logo/fav-logo-prim.png')}}">


    <link href="{{ URL::asset('assets/css/bootstrap.min.css')}}" id="bootstrap-light" rel="stylesheet"
        type="text/css" />
    <link href="{{ URL::asset('assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/css/app.min.css')}}" id="app-light" rel="stylesheet" type="text/css" />


</head>

<body>

    {{-- <div class="home-btn d-none d-sm-block">
        <a href="/derma" class="text-dark"><i class="fas fa-home h2"></i></a>
    </div> --}}

    <section class="my-5">
        <div class="container-alt container">
            <div class="row justify-content-center">
                <div class="col-10 text-center">
                    <div class="home-wrapper mt-5">
                        <div class="mb-4">
                            {{-- <img src="{{ URL::asset('assets/images/logo/fav-logo-prim.png')}}" alt="logo" height="50" style="color: black" /> --}}
                        </div>

                        <div class="maintenance-img">
                            <img src="{{ URL::asset('assets/images/maintenance.png')}}" alt="" class="img-fluid mx-auto d-block">
                        </div>
                        <h3 class="mt-4">Site is Under Maintenance</h3>
                        <p>Please check back in sometime. <a href="/derma" >Click here</a></p>
                        <p></p>

                        <div class="row">
                            {{-- <div class="text-center col-md-4">
                                <div class="card mt-4 maintenance-box">
                                    <div class="card-body">
                                        <i class="mdi mdi-airplane-takeoff h2"></i>
                                        <h6 class="text-uppercase mt-3">Why is the Site Down?</h6>
                                        <p class="text-muted mt-3">Still under maintenance.</p>
                                    </div>
                                </div>
                            </div> --}}
                            {{-- <div class="text-center col-md-4">
                                <div class="card mt-4 maintenance-box">
                                    <div class="card-body">
                                        <i class="mdi mdi-clock-alert h2"></i>
                                        <h6 class="text-uppercase mt-3">
                                            What is the Downtime?</h6>
                                        <p class="text-muted mt-3">Contrary to popular belief, Lorem Ipsum is not
                                            simply random text. It has roots in a piece of classical.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center col-md-4">
                                <div class="card mt-4 maintenance-box">
                                    <div class="card-body">
                                        <i class="mdi mdi-email h2"></i>
                                        <h6 class="text-uppercase mt-3">
                                            Do you need Support?</h6>
                                        <p class="text-muted mt-3">If you are going to use a passage of Lorem
                                            Ipsum, you need to be sure there isn't anything embar.. <a
                                                href="mailto:no-reply@domain.com"
                                                class="text-decoration-underline">no-reply@domain.com</a></p>
                                    </div>
                                </div>
                            </div> --}}
                        </div>
                        <!-- end row -->
                    </div>
                </div>
            </div>
        </div>
    </section>



    <!-- JAVASCRIPT -->
    <script src="{{ URL::asset('assets/js/app.js')}}"></script>

    <script src="{{ URL::asset('assets/libs/jquery/jquery.min.js')}}"></script>
    <script src="{{ URL::asset('assets/libs/jquery-ui/jquery-ui.min.js')}}"></script>
    <script src="{{ URL::asset('assets/libs/bootstrap/bootstrap.min.js')}}"></script>
    <script src="{{ URL::asset('assets/libs/metismenu/metismenu.min.js')}}"></script>
    <script src="{{ URL::asset('assets/libs/simplebar/simplebar.min.js')}}"></script>
    <script src="{{ URL::asset('assets/libs/node-waves/node-waves.min.js')}}"></script>
</body>

</html>