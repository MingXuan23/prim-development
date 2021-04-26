<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link href="{{ URL::asset('assets/css/bootstrap.min.css') }}" id="bootstrap-light" rel="stylesheet"
        type="text/css" />
    <link href="{{ URL::asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/css/app.min.css') }}" id="app-light" rel="stylesheet" type="text/css" />
    <style>
        span{
            font-size: 22px;
        }
    </style>
    <title>Document</title>
</head>

<body>
    <div class="container">
        <div class="card rounded-xl mt-4">
            <div class="card-body shadow rounded mb-1" style="background-color:#323447">
                <center>
                    <img src="{{ URL::asset('assets/images/logo/prim.svg') }}" alt="" height="50">
                </center>
            </div>
            <div class="card-text p-4">
                <h4 class=" mb-3">Derma Kilat Masjid Ilhami</h4>
                <hr>
                <div class="row">
                    <div class="col-6">
                        <div class="row">
                            <div class="col-12">
                                <h4>Buku Latihan</h4>
                            </div>
                            <div class="col-12">
                                <h5 class="mt-0" style="color:#8699ad">
                                    Kuantiti x2
                                </h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                       <h4 class="float-right">RM5.00</h4> 
                    </div>
                    <div class="col-6">
                        <div class="row">
                            <div class="col-12">
                                <h4>Alat Tulis</h4>
                            </div>
                            <div class="col-12">
                                <h5 class="mt-0" style="color:#8699ad">
                                    Kuantiti x4
                                </h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="float-right">RM30.00</h4>
                    </div>
                </div>
                <hr>
                    <div class="row mb-4">
                        <div class="col-6">
                            <h4 class=" mb-3">Jumlah Amaun</h4>
                        </div>
                        <div class="col-6">
                            <h4 class="float-right mb-3">RM35.00</h4>
                        </div>
                    </div>     
            <div class="form-group">
                <label for="sel1">Please Select Bank:</label>
                <select class="form-control" id="sel1">
                    <option value="Maybank2U">Maybank2U</option>
                    <option value="Bank Islam">Bank Islam</option>
                    <option value="Bank Muamalat">Bank Muamalat</option>
                    <option value="Public Bank">Public Bank</option>
                    <option value="Maybank2E">Maybank2E</option>
                </select>
              </div>
              <ul>
                  <li>              
                      <p>Minimum Transaction is RM1 and Maximum Transaction is RM30,000.</p>
                  </li>
              </ul>
                <button class="btn btn-primary float-right mt-3 w-100 p-2" style="font-size:18px" type="submit">Teruskan Pembayaran</button>
            </div>

        </div>

    </div>
    <script>

    </script>
</body>

</html>
