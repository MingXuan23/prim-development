<!doctype html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <title>500 | PRIM</title>
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
    <!-- Begin page -->
    <div class="authentication-bg d-flex align-items-center pb-0 vh-100">
        <div class="content-center w-100">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xl-10">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-lg-4 ms-auto">
                                        <div class="ex-page-content">
                                            <h1 class="text-dark display-1 mt-4">500</h1>
                                            <h4 class="mb-4">Internal Server Error</h4>
                                            <p class="mb-5">It will be as simple as Occidental in fact, it will be
                                                Occidental to an English person</p>
                                            {{-- <a class="btn btn-primary mb-5 waves-effect waves-light" href="index.html"><i class="mdi mdi-home"></i> Back to Dashboard</a> --}}
                                        </div>

                                    </div>
                                    <div class="col-lg-5 mx-auto">
                                        <img src="assets/images/error.png" alt="" class="img-fluid mx-auto d-block">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end card -->
                    </div>
                </div>
            </div>
            <!-- end container -->
        </div>

    </div>
    <!-- end error page -->

    <!-- JAVASCRIPT -->
    {{-- <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script> --}}

    <script src="{{ URL::asset('assets/js/app.js')}}"></script>

    <script src="{{ URL::asset('assets/libs/jquery/jquery.min.js')}}"></script>
    <script src="{{ URL::asset('assets/libs/jquery-ui/jquery-ui.min.js')}}"></script>
    <script src="{{ URL::asset('assets/libs/bootstrap/bootstrap.min.js')}}"></script>
    <script src="{{ URL::asset('assets/libs/metismenu/metismenu.min.js')}}"></script>
    <script src="{{ URL::asset('assets/libs/simplebar/simplebar.min.js')}}"></script>
    <script src="{{ URL::asset('assets/libs/node-waves/node-waves.min.js')}}"></script>

</body>

</html>