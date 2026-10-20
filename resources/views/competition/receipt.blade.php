<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Parental Relationship Information Management" name="description" />
    <meta content="UTeM" name="author" />
    <title>PRiM | Resit Pembayaran</title>

    <link rel="shortcut icon" href="{{ URL::asset('assets/images/logo/fav-logo-prim.png')}}">
    <link href="{{ URL::asset('assets/libs/chartist/chartist.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/css/checkbox.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ URL::asset('assets/css/accordion.css') }}" rel="stylesheet" type="text/css" />
    @include('layouts.head')

    <style>
        .table td,
        .table th {
            padding: .3rem !important;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row mt-3">
            <div class="col-12">
                <div class="card mb-1">
                    <div class="card-body py-5">
                        <div class="row">
                            @php 
                                $org = $get_participant_to_organization->first();
                                $type = $org->participateType;
                            @endphp

                            <div class="col-lg-2 col-sm-12 p-0">
                                <center>
                                    @if($org && $org->organization_picture)
                                        <img src="{{ URL::asset('/organization-picture/' . $org->organization_picture) }}"
                                            class="img-fluid" alt="" />
                                    @endif
                                </center>
                            </div>
                            <div class="col-lg-6 col-sm-12 p-0">
                                <h4>{{ $org->nama }}</h4>

                                @if($org && !empty($org->address))
                                    <p>{{ $org->address }},
                                        <br />
                                        {{ $org->postcode }} {{ $org->city }},
                                        {{ $org->state }}
                                    </p>
                                @endif
                            </div>
                            <div class="col-lg-4 col-sm-12">
                                <table style="width: 100%">
                                    <tr style="background-color:#e9ecef">
                                        <th colspan="6" class="text-center">Resit</th>
                                    </tr>
                                    <tr>
                                        <td>No Resit</td>
                                        <td style="width: 20px">:</td>
                                        <td>{{ $get_transaction->description }}</td>
                                    </tr>
                                    <tr>
                                        <td>No Transaksi FPX</td>
                                        <td>:</td>

                                        <td>{{ $get_transaction->transac_no }}</td>
                                    </tr>
                                    <tr>
                                        <td>Tarikh</td>
                                        <td>:</td>
                                        <td>{{ \Carbon\Carbon::parse($get_transaction->datetime_created)->format('j M Y H:i:s A') }}</td>
                                    </tr>
                                    <tr>
                                        <td>Dikemaskini</td>
                                        <td>:</td>
                                        <td>{{ isset($get_transaction->datetime_of_success) ? \Carbon\Carbon::parse($get_transaction->datetime_of_success)->format('j M Y H:i:s A') : "" }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-12 pt-3">
                                <table style="width:100%" class="infotbl">
                                    <tr style="background-color:#e9ecef">
                                        <th colspan="9" class="text-center">Maklumat Pembayar</th>
                                    </tr>
                                    <tr>
                                        <td class="py-3">Nama</td>
                                        <td class="py-2">:</td>
                                        <td class="py-2 w-50">{{ $loginUser->name }}</td>
                                        <td class="py-2" colspan="3"></td>
                                        <td class="py-2">No. Kad Pengenalan
                                        </td>
                                        <td class="py-2">:</td>
                                        <td class="py-2">
                                            @if($loginUser && $loginUser->icno)
                                                {{ $loginUser->icno }}
                                            @else
                                                {{ $loginUser->telno }}
                                            @endif    
                                        </td>
                                    </tr>
                                    <tr style="background-color:#e9ecef">
                                        <th colspan="9" class="text-center">Maklumat Yuran</th>
                                    </tr>
                                    {{-- <tr style="border-bottom:2px solid #e0e0e0">
                                        <td colspan="9" class="pt-2" style="font-size: 18px">
                                            Syuhaidi Bin Halim
                                        </td>
                                    </tr> --}}
                                </table>

                                <table class="table table-bordered table-striped" style="margin-top: 30px;">
                                    <tr style="text-align: center">
                                        <th style="width:3%">Bil.</th>
                                        <th>Pertandingan</th>
                                        <th style="width:10%">Kuantiti</th>
                                        <th style="width:20%">Amaun per item (RM)</th>
                                        <th style="width:20%">Amaun (RM)</th>
                                    </tr>

                                    @foreach ($get_participant_to_organization as $index => $item)
                                        @php
                                            $cleanIc = str_replace('-', '', $item->icNo ?? '');
                                            $isMalaysia = ctype_digit($cleanIc) && strlen($cleanIc) === 12;
                                            
                                            $fee = $isMalaysia ? $item->nationalFees : $item->internationalFees;
                                            $quantity = 1;
                                            $itemTotal = $fee * $quantity;
                                        @endphp
                                        
                                        <tr>
                                            <td style="text-align: center"> {{ $index + 1 }}.</td>
                                            <td>
                                                <div class="pl-2"> {{ $item->competitionTitle }} - {{ $type == 'Team' ? $item->name . ' (' . $item->groupName . ')' : $item->name }} </div>
                                            </td>
                                            <td style="text-align: center">{{ $quantity }}</td>
                                            <td style="text-align: center">

                                                {{  number_format((float) $fee, 2, '.', '') }} </td>
                                            <td style="text-align: center">

                                                {{  number_format((float) $itemTotal, 2, '.', '')  }}</td>
                                        </tr>
                                    @endforeach

                                    <tr>
                                        <td></td>
                                        <td colspan="3" style="text-align:center"><b>Jumlah</b> </td>
                                        <td style="text-align:center">
                                            <b>{{ number_format($get_transaction->amount, 2)  }}</b>

                                        </td>
                                    </tr>

                                </table>

                                <table style="width:100%" class="infotbl">
                                    <tr>
                                        <td></td>
                                        <td colspan="3" style="text-align:right">
                                            Caj yang dikenakan oleh organisasi (RM)
                                        </td>
                                        <td style="text-align:center;width:20%">
                                            {{  number_format((float) $org->fixed_charges, 2, '.', '') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td colspan="3" style="text-align:right; font-size:18px;"><b>Jumlah Bayaran (RM)</b> <br>
                                            <!-- @if($get_participant_to_organization->count() > 1)
                                                <small class="text-muted" style="font-size: 12px; font-weight: normal;">
                                                    (Termasuk bayaran untuk {{ $get_participant_to_organization->count() }} penyertaan pertandingan)
                                                </small> 
                                            @endif -->
                                        </td>
                                        <td style="text-align:center; width:20%; font-size:18px">
                                            <b>{{  number_format((float) $get_transaction->amount + (float) ($org->fixed_charges ?? 0), 2, '.', '') }}</b>
                                        </td>
                                    </tr>
                                </table>

                                <div class="col-12 pt-5 text-center">
                                    <button class="btn btn-primary p-2 w-10 mx-2 btn-fill" style="font-size:18px"
                                        onclick="window.print();">
                                        <span class="mdi mdi-file-pdf"> Print </span>
                                    </button>
                                    <a href="/ContestPro/record-registration">
                                        <button class="btn btn-danger p-2 w-10 mx-2" style="font-size:18px;">
                                            <span class="mdi mdi-chevron-left-circle"> Kembali</span>
                                        </button>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>