<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Parental Relationship Information Management" name="description" />
    <meta content="UTeM" name="author" />
    <title>PRiM | Resit Pembayaran</title>

    <link rel="shortcut icon" href="{{ URL::asset('assets/images/logo/fav-logo-prim.png')}}">

    @include('layouts.head')

    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f8f9fa;
        }

        .card {
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .card-body {
            padding: 1.5rem;
        }

        h2,
        h4,
        h5 {
            margin-bottom: 0.5rem;
        }

        .table th,
        .table td {
            padding: 0.5rem !important;
            font-size: 14px;
        }

        .btn {
            font-size: 16px;
            padding: 10px 20px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 100px 12px 1fr 160px 12px 1fr;
            column-gap: 12px;
            row-gap: 6px;
            align-items: center;
        }

        .info-grid .label {
            white-space: nowrap;
        }

        .info-grid .sep {
            text-align: center;
        }

        .info-grid .value {
            word-break: break-word;
        }

        @media (max-width: 767.98px) {
            .info-grid {
                grid-template-columns: 140px 10px 1fr;
            }
        }

        @media (max-width: 575.98px) {
            .table-responsive {
                font-size: 12px;
            }

            .btn {
                font-size: 14px;
                padding: 8px 16px;
            }

        }

        .receipt-header {
            text-align: center;
            background-color: #e9ecef;
            border-radius: 6px;
            padding: 12px 0;
        }

        .section-title {
            background-color: #e9ecef;
            font-weight: bold;
            text-align: center;
            padding: 6px 0;
            margin-top: 10px;
            border-radius: 4px;
            letter-spacing: 0.5px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .table th,
        .table td {
            border: 1px solid #ddd;
            padding: 6px 10px;
            font-size: 14px;
        }

        .table th {
            background-color: #f2f3f5;
            font-weight: 600;
            text-align: center;
        }

        .table-bordered tr:nth-child(even) {
            background-color: #fafafa;
        }

        .infotbl td {
            border: none !important;
            padding: 4px 6px;
        }

        .infotbl td:first-child {
            width: 160px;
        }

        .infotbl td:last-child {
            text-align: left;
        }

        .total-section td {
            font-size: 15px;
            padding-top: 8px;
            padding-bottom: 8px;
        }

        .total-section tr:last-child td {
            font-size: 18px;
            font-weight: bold;
        }

        .address-section {
            margin-bottom: 6px !important;
        }

        .receipt-info {
            margin-top: 8px !important;
        }

        .receipt-info table tr:first-child td {
            padding-top: 6px;
        }

        .payer-info {
            margin-top: 6px !important;
        }

        .infotbl tr td {
            padding-top: 3px !important;
            padding-bottom: 3px !important;
        }

        .dashed-line {
            border-bottom: 2px dashed #999;
            margin: 10px 0;
        }

        .org-title {
            margin-top: 10px !important;
        }

        .button-row {
            white-space: nowrap;
        }

        .button-row button {
            display: inline-flex;
            margin: 0 8px;
            white-space: nowrap;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row mt-3">
            <div class="col-12">
                <div class="card mb-1">
                    <div class="card-body py-4">
                        <div class="receipt-header">
                            <h3>Resit Pembayaran Yuran</h3>
                        </div>
                        <div class="row">
                            <div class="col-12 text-center mb-2 mt-3">
                                @php
                                $imageUrl = 'https://prim.my/organization-picture/' . $get_organization->organization_picture;

                                $imageExists = false;
                                if (!empty($get_organization->organization_picture)) {
                                $headers = @get_headers($imageUrl);
                                $imageExists = $headers && strpos($headers[0], '200') !== false;
                                }
                                @endphp

                                @if($imageExists)
                                <img src="{{ $imageUrl }}" height="60" alt="">
                                @endif
                            </div>

                            <div class="col-12 text-center address-section">
                                <h4>{{ $get_organization->nama }}</h4>
                                <p class="mb-1">{{ $get_organization->address }},<br />
                                    {{ $get_organization->postcode }} {{ $get_organization->city }}, {{ $get_organization->state }}
                                </p>
                            </div>

                            <div class="col-12 receipt-info">
                                <div class="section-title">Maklumat Resit</div>
                                <table style="width: 100%; margin-top: 6px;">
                                    <tr>
                                        <td style="width:180px;">No Resit</td>
                                        <td style="width: 20px;">:</td>
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
                                        <td>{{ $get_transaction->datetime_created->format('j M Y H:i:s A')}}</td>
                                    </tr>
                                </table>
                            </div>

                            <div class="col-12 payer-info">
                                <div class="section-title">Maklumat Pembayar</div>

                                <div class="info-grid">
                                    <div class="label">Nama</div>
                                    <div class="sep">:</div>
                                    <div class="value">{{ $getparent->name }}</div>

                                    <div class="label">
                                        @if($getparent->icno)
                                        No. Kad Pengenalan
                                        @else
                                        No. Telefon
                                        @endif
                                    </div>
                                    <div class="sep">:</div>
                                    <div class="value">
                                        @if($getparent->icno)
                                        {{ $getparent->icno }}
                                        @else
                                        {{ $getparent->telno }}
                                        @endif
                                    </div>
                                </div>


                                <div class="dashed-line"></div>

                                @if (count($getfees_categoryA) != 0)
                                <div class="org-title pt-2" style="border-bottom:2px solid #e0e0e0;font-size: 18px">
                                    <strong>{{ $get_organization->nama }}</strong>
                                </div>

                                <div class="pt-2 pb-2">Kategori A</div>
                                <table class="table table-bordered table-striped">
                                    <tr style="text-align: center">
                                        <th style="width:3%">Bil.</th>
                                        <th>Item</th>
                                        <th style="width:20%">Amaun (RM)</th>
                                    </tr>
                                    @foreach ($getfees_categoryA as $item)
                                    <tr>
                                        <td style="text-align: center"> {{ $loop->iteration }}.</td>
                                        <td>
                                            <div class="pl-2"> {{ $item->name }} x {{ $item->quantity }} </div>
                                        </td>
                                        <td style="text-align: center">
                                            {{ number_format((float)$item->totalAmount, 2, '.', '')  }}
                                        </td>
                                    </tr>
                                    @endforeach

                                    <tr>
                                        <td></td>
                                        <td style="text-align:center"><b>Jumlah</b></td>
                                        <td style="text-align:center">
                                            <b>{{ number_format($getfees_categoryA->sum('totalAmount'), 2) }}</b>
                                        </td>
                                    </tr>
                                </table>
                                @endif

                                @if (count($get_student) != 0)
                                @foreach ($get_student as $student)
                                <div class="pt-2" style="border-bottom:2px solid #e0e0e0;font-size: 18px">
                                    <strong>{{ $student->nama }} ({{ $student->classname }})</strong>
                                </div>

                                @foreach ($get_category->where('studentid', $student->id) as $category)
                                <div class="pt-2 pb-2">{{ $category->category }}</div>

                                <table class="table table-bordered table-striped">
                                    <tr style="text-align: center">
                                        <th style="width:3%">Bil.</th>
                                        <th>Item</th>
                                        <th style="width:20%">Amaun (RM)</th>
                                    </tr>
                                    @foreach ($get_fees->where('studentid', $student->id)->where('category', $category->category) as $item)
                                    <tr>
                                        <td style="text-align: center">{{ $loop->iteration }}.</td>
                                        <td>
                                            <div class="pl-2">{{ $item->name }} x {{ $item->quantity }}</div>
                                        </td>
                                        <td style="text-align: center">
                                            {{ number_format((float)$item->totalAmount, 2, '.', '') }}
                                        </td>
                                    </tr>
                                    @endforeach

                                    <tr>
                                        <td></td>
                                        <td style="text-align:center"><b>Jumlah</b></td>
                                        <td style="text-align:center">
                                            <b>{{ number_format($get_fees->where('studentid', $student->id)->where('category', $category->category)->sum('totalAmount'), 2) }}</b>
                                        </td>
                                    </tr>
                                </table>
                                @endforeach
                                @endforeach
                                @endif

                                <table class="table infotbl total-section mt-4">
                                    <tr>
                                        <td style="text-align:right;">Caj yang dikenakan oleh organisasi (RM)</td>
                                        <td style="text-align:right;width:20%;">
                                            {{ number_format((float)$get_organization->fixed_charges, 2, '.', '') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="text-align:right;"><b>Jumlah Bayaran (RM)</b></td>
                                        <td style="text-align:right;">
                                            <b>{{ number_format((float)$get_transaction->amount, 2, '.', '') }}</b>
                                        </td>
                                    </tr>
                                </table>

                                <div class="col-12 pt-5 text-center button-row">
                                    <button id="downloadBtn" class="btn btn-primary p-2 btn-fill" style="font-size:18px">
                                        <span class="mdi mdi-download"> Muat Turun </span>
                                    </button>
                                    <button
                                        id="shareBtn"
                                        class="btn btn-success p-2 btn-fill"
                                        style="font-size:18px"
                                        onclick="window.ShareChannel?.postMessage('share')">
                                        <span class="mdi mdi-share"> Kongsi </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('downloadBtn').addEventListener('click', function() {
            const downloadUrl = '/mobile/receipt/download/{{ $get_transaction->id }}';
            window.location.href = downloadUrl;
        });
    </script>
</body>

</html>