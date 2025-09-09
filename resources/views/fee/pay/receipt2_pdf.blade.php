<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Resit Pembayaran Yuran</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            font-size: 14px;
        }

        .container {
            max-width: 1000px;
            margin: auto;
            padding: 20px;
        }

        .card {
            border: none;
            border-radius: 0;
            overflow: hidden;
        }

        .card-body {
            padding: 0;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -15px;
        }

        .col-12,
        .col-lg-3,
        .col-lg-5,
        .col-lg-4,
        .col-sm-12,
        .col-6,
        .col-4,
        .col-1,
        .col-7 {
            position: relative;
            width: 100%;
            padding: 0 15px;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .font-bold,
        b,
        strong {
            font-weight: bold;
        }

        h2,
        h4,
        h5 {
            margin: 0.5rem 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        th,
        td {
            padding: 6px;
            text-align: left;
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid #ddd;
        }

        .table-striped tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .infotbl {
            margin-bottom: 0;
        }

        .pl-2 {
            padding-left: 1rem;
        }

        .w-200 {
            width: 200px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            text-align: center;
            text-decoration: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
            border: none;
        }

        .btn-fill {
            width: 100%;
        }

        .g-0 {
            gap: 0;
        }

        .mdi-file-pdf::before {
            content: "📄";
        }

        .w-10 {
            width: 10rem;
        }

        .mx-2 {
            margin: 0 1rem;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row mt-3">
            <div class="col-12">
                <div class="card mb-1">
                    <div class="card-body py-5">
                        <div class="row justify-content-center" style="background-color:#e9ecef">
                            <h2 class="text-center">Resit Pembayaran Yuran</h2>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-lg-3 col-sm-12 p-0">
                                <center>
                                    @if($get_organization->organization_picture)
                                    @php
                                    $logoPath = public_path('organization-picture/' . $get_organization->organization_picture);
                                    @endphp
                                    @if(file_exists($logoPath))
                                    <img src="{{ $logoPath }}" height="80" alt="{{ $get_organization->nama }}" />
                                    @endif
                                    @endif
                                </center>
                            </div>
                            <div class="col-lg-5 col-sm-12 p-0">
                                <h4>{{ $get_organization->nama }}</h4>
                                <p>{{ $get_organization->address }},
                                    <br />
                                    {{ $get_organization->postcode }} {{ $get_organization->city }}, {{ $get_organization->state }}
                                </p>
                            </div>
                            <div class="col-lg-4 col-sm-12">
                                <table style="width: 100%">
                                    <tr style="background-color:#e9ecef">
                                        <th colspan="6" class="text-center">Maklumat Resit</th>
                                    </tr>
                                    <tr>
                                        <td>No Resit</td>
                                        <td style="width: 20px">:</td>
                                        <td>{{ $get_transaction->description }}</td>
                                    </tr>
                                    <tr>
                                        <td>Tarikh</td>
                                        <td>:</td>
                                        <td>{{ $get_transaction->datetime_created->format('j M Y H:i:s A')}}</td>
                                    </tr>
                                </table>
                            </div>

                            <div class="col-12 pt-3">
                                <table style="width: 100%">
                                    <tr>
                                        <th colspan="6" class="text-center" style="background-color:#e9ecef">
                                            Maklumat Pembayar
                                        </th>
                                    </tr>
                                </table>



                                <div class="row g-0">
                                    <div class="col-sm-12 col-lg-6">
                                        <table class="table table-borderless infotbl mb-0">
                                            <tr>
                                                <td class="col-4"><strong>Nama</strong></td>
                                                <td class="col-1">:</td>
                                                <td class="col-7">{{ $getparent->name }}</td>
                                            </tr>
                                        </table>
                                    </div>

                                    <div class="col-sm-12 col-lg-6">
                                        <table class="table table-borderless infotbl mb-0">
                                            <tr>
                                                <td class="col-4"><strong>No. Kad Pengenalan</strong></td>
                                                <td class="col-1">:</td>
                                                <td class="col-7">
                                                    @if($getparent->icno)
                                                    {{ $getparent->icno }}
                                                    @else
                                                    {{ $getparent->telno }}
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                <br>

                                @if (count($getfees_categoryA) != 0)
                                <div class="pt-2" style="border-bottom:2px solid #e0e0e0;font-size: 20px">
                                    <strong>{{ $get_organization->nama }}</strong>
                                </div>

                                <div class="pt-2 pb-2">
                                    Kategori A
                                </div>

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
                                            {{ number_format((float)$item->totalAmount, 2, '.', '') }}
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
                                <div class="pt-2" style="border-bottom:2px solid #e0e0e0;font-size: 20px">
                                    <strong> {{ $student->nama }} ({{ $student->classname }})</strong>
                                </div>

                                @foreach ($get_category->where('studentid', $student->id) as $category)
                                <div class="pt-2 pb-2">
                                    {{ $category->category }}
                                </div>

                                <table class="table table-bordered table-striped">
                                    <tr style="text-align: center">
                                        <th style="width:3%">Bil.</th>
                                        <th>Item</th>
                                        <th style="width:20%">Amaun (RM)</th>
                                    </tr>
                                    @foreach ($get_fees->where('studentid', $student->id)->where('category', $category->category) as $item)
                                    <tr>
                                        <td style="text-align: center"> {{ $loop->iteration }}.</td>
                                        <td>
                                            <div class="pl-2"> {{ $item->name }} x {{ $item->quantity }} </div>
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

                                <table style="width:100%" class="infotbl">
                                    <tr>
                                        <td></td>
                                        <td colspan="3" style="text-align:right">
                                            Caj yang dikenakan oleh organisasi (RM)
                                        </td>
                                        <td style="text-align:center;width:20%">
                                            {{ number_format((float)$get_organization->fixed_charges, 2, '.', '') }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td colspan="3" style="text-align:right;font-size:18px;"><b>Jumlah Bayaran (RM)</b></td>
                                        <td style="text-align:center; width:20%; font-size:18px">
                                            <b>{{ number_format((float)$get_transaction->amount, 2, '.', '') }}</b>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>