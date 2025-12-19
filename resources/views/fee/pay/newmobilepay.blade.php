<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="{{ URL::asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
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
    <div class="container">
        <div class="card rounded-xl mt-4">
            <div class="card-body shadow rounded mb-1" style="background-color:#323447">
                <center>
                    <img src="{{ URL::asset('assets/images/logo/prim.svg') }}" alt="" height="50">
                </center>
            </div>

            <div class="card-text p-4">
                <h4 class="mb-3 text-center">
                    {{ $getorganization->nama ?? 'Organization' }}
                </h4>

                <form method="POST" action="{{ route('directpayIndex') }}">
                    @csrf

                    <input type="hidden" name="source" value="mobile">

                    <input type="hidden" name="amount" value="{{ number_format($grandTotal, 2, '.', '') }}">
                    <input type="hidden" name="o_id" value="{{ $getorganization->id ?? '' }}">
                    <input type="hidden" name="desc" value="School_Fees">
                    <input type="hidden" name="user_id" value="{{ $user_id }}">

                    @foreach ($original_student_fees_ids as $id)
                    <input type="hidden" name="student_fees_id[]" value="{{ $id }}">
                    @endforeach

                    @foreach ($fno_ids as $fnoId)
                    <input type="hidden" name="parent_fees_id[]" value="{{ $fnoId }}">
                    @endforeach

                    @if ($getfees_category_A_byparent && $getfees_category_A_byparent->isNotEmpty())
                    <hr>
                    <h4 class="mb-3 mt-3">--Kategori A--</h4>
                    <div class="row">
                        @foreach($getfees_category_A_byparent as $fee)
                        <div class="col-6">
                            <h4>{{ $loop->iteration }}. {{ $fee->name }}</h4>
                            <h5 class="mt-0" style="color:#8699ad">Kuantiti x{{ $fee->quantity }}</h5>
                            <h5 class="mt-0" style="color:#8699ad">{!! nl2br(e($fee->desc)) !!}</h5>
                        </div>
                        <div class="col-6">
                            <h4 class="float-right">
                                RM {{ number_format($fee->quantity * $fee->price, 2) }}
                            </h4>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    @if ($getstudentfees && $getstudentfees->isNotEmpty())
                    @php $groupedFees = $getstudentfees->groupBy('studentid'); @endphp
                    @foreach($groupedFees as $studentId => $fees)
                    <hr>
                    @php $student = $getstudent->firstWhere('studentid', $studentId); @endphp
                    <h4 class="mb-3 text-center">{{ $student->studentname ?? 'Student' }}</h4>
                    <hr>
                    @php $feesByCategory = $fees->groupBy('category'); @endphp
                    @foreach($feesByCategory as $category => $categoryFees)
                    <h4 class="mb-3 mt-3">--{{ $category }}--</h4>
                    <div class="row">
                        @foreach($categoryFees as $fee)
                        <div class="col-6">
                            <h4>{{ $loop->iteration }}. {{ $fee->name }}</h4>
                            <h5 class="mt-0" style="color:#8699ad">Kuantiti x{{ $fee->quantity }}</h5>
                            <h5 class="mt-0" style="color:#8699ad">{!! nl2br(e($fee->desc)) !!}</h5>
                        </div>
                        <div class="col-6">
                            <h4 class="float-right">
                                RM {{ number_format($fee->quantity * $fee->price, 2) }}
                            </h4>
                        </div>
                        @endforeach
                    </div>
                    @endforeach
                    @endforeach
                    @endif

                    <hr>
                    <div class="row mb-4">
                        <div class="col-6">
                            <h5>Caj yang dikenakan</h5>
                        </div>
                        <div class="col-6">
                            <h5 class="float-right">RM {{ number_format($fixedCharges, 2) }}</h5>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-6">
                            <h4>Jumlah Bayaran</h4>
                        </div>
                        <div class="col-6">
                            <h4 class="float-right">
                                RM <span style="font-size:22px;">{{ number_format($grandTotal, 2) }}</span>
                            </h4>
                        </div>
                    </div>

                    <ul>
                        <li>
                            <p>Minimum RM1, Maximum RM30,000.</p>
                        </li>
                    </ul>

                    <button id="bayarBtn"
                        class="btn btn-primary float-right mt-3 w-100 p-2"
                        style="font-size:18px"
                        type="submit">
                        Teruskan Pembayaran
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>