<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="{{ URL::asset('assets/css/bootstrap.min.css') }}" id="bootstrap-light" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/css/app.min.css') }}" id="app-light" rel="stylesheet" type="text/css" />
    <style>
        span {
            font-size: 1.09375rem;
            font-weight: bolder;
        }
    </style>
    <title>PRiM | Pembayaran</title>
    <link rel="shortcut icon" href="{{ URL::asset('assets/images/logo/fav-logo-prim.png')}}">
</head>

<body>
    @php
    $totalA = 0;
    $totalBC = 0;
    $fixedCharges = $getorganization && isset($getorganization->fixed_charges) ? $getorganization->fixed_charges : 0;
    @endphp

    <div class="container">
        <div class="card rounded-xl mt-4">
            <div class="card-body shadow rounded mb-1" style="background-color:#323447">
                <center>
                    <img src="{{ URL::asset('assets/images/logo/prim.svg') }}" alt="" height="50">
                </center>
            </div>

            <div class="card-text p-4">
                <h4 class="mb-3" style="text-align: center">
                    {{ $getorganization && $getorganization->nama ? $getorganization->nama : 'Organization' }}
                </h4>

                @if ($getfees_category_A_byparent && $getfees_category_A_byparent->isNotEmpty())
                <hr>
                <h4 class="mb-3 mt-3">--Kategori A--</h4>
                <div class="row">
                    @foreach($getfees_category_A_byparent as $fee)
                    <div class="col-6">
                        <div class="row">
                            <div class="col-12">
                                <h4>{{ $loop->iteration }}. {{ $fee->name }}</h4>
                            </div>
                            <div class="col-12">
                                <h5 class="mt-0" style="color:#8699ad">
                                    Kuantiti x{{ $fee->quantity }}
                                </h5>
                                <h5 class="mt-0" style="color:#8699ad">
                                    {!! nl2br($fee->desc) !!}
                                </h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        @php
                        $totalA += $fee->quantity * $fee->price;
                        @endphp
                        <h4 class="float-right">RM {{ number_format($fee->quantity * $fee->price, 2) }}</h4>
                    </div>
                    @endforeach
                </div>
                @if ($getstudentfees && $getstudentfees->isEmpty())
                <hr>
                @endif

                @foreach ($get_fees_by_parent as $fee)
                <input type="hidden" name="parent_fees_id[]" value="{{ $fee->id }}">
                @endforeach
                @endif

                @if ($getstudentfees && $getstudentfees->isNotEmpty())
                <hr>
                @php
                $groupedFees = $getstudentfees->groupBy('studentid');
                @endphp

                @foreach($groupedFees as $studentId => $fees)
                @php
                $student = $getstudent->firstWhere('studentid', $studentId);
                @endphp
                <h4 class="mb-3" style="text-align: center">{{ $student ? $student->studentname : 'Student' }}</h4>
                <hr>

                @php
                $feesByCategory = $fees->groupBy('category');
                @endphp

                @foreach($feesByCategory as $category => $categoryFees)
                <h4 class="mb-3 mt-3">--{{ $category }}--</h4>
                <div class="row">
                    @foreach($categoryFees as $fee)
                    <div class="col-6">
                        <div class="row">
                            <div class="col-12">
                                <h4>{{ $loop->iteration }}. {{ $fee->name }}</h4>
                            </div>
                            <div class="col-12">
                                <h5 class="mt-0" style="color:#8699ad">
                                    Kuantiti x{{ $fee->quantity }}
                                </h5>
                                <h5 class="mt-0" style="color:#8699ad">
                                    {!! nl2br($fee->desc) !!}
                                </h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        @php
                        $totalBC += $fee->quantity * $fee->price;
                        @endphp
                        <h4 class="float-right">RM {{ number_format($fee->quantity * $fee->price, 2) }}</h4>
                    </div>
                    @endforeach
                </div>
                @endforeach
                <hr>
                @endforeach

                @foreach ($getstudentfees as $fee)
                <input type="hidden" name="student_fees_id[]" value="{{ $fee->student_fees_id }}">
                @endforeach
                @endif

                @php
                $grandTotal = $totalA + $totalBC + $fixedCharges;
                @endphp


                <div class="row mb-4">
                    <div class="col-6">
                        <h5 class="mb-3">Caj yang dikenakan</h5>
                    </div>
                    <div class="col-6">
                        <h5 class="float-right mb-3">RM {{ number_format($fixedCharges, 2) }}</h5>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-6">
                        <h4 class="mb-3">Jumlah Bayaran</h4>
                    </div>
                    <div class="col-6">
                        <h4 class="float-right mb-3">RM <span style="font-size: 22px;">{{ number_format($grandTotal, 2) }}</span></h4>
                    </div>
                </div>

                <ul>
                    <li>
                        <p>Minimum Transaction is RM1 and Maximum Transaction is RM30,000.</p>
                    </li>
                </ul>

                <form method="POST" action="{{ route('directpayIndex') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="amount" value="{{ $grandTotal }}">
                    <input type="hidden" name="o_id" value="{{ $getorganization ? $getorganization->id : '' }}">
                    <input type="hidden" name="desc" value="School_Fees">
                    <input type="hidden" name="user_id" value="{{ $user_id ?? '' }}">

                    <button id="bayarBtn" class="btn btn-primary float-right mt-3 w-100 p-2" style="font-size:18px" type="submit">
                        Teruskan Pembayaran
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>