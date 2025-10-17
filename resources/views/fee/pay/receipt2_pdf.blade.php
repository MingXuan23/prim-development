<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Resit Pembayaran Yuran</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 13px;
            color: #000;
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

        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 20px;
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 13px;
        }

        th,
        td {
            padding: 6px 8px;
            border: 1px solid #ddd;
            vertical-align: top;
        }

        th {
            background-color: #e9ecef;
            font-weight: bold;
        }

        .table-title {
            background-color: #e9ecef;
            text-align: center;
            font-weight: bold;
        }

        .section-title {
            font-size: 15px;
            font-weight: bold;
            border-bottom: 2px solid #e0e0e0;
            margin-top: 15px;
            margin-bottom: 10px;
            padding-bottom: 3px;
        }

        .org-header {
            width: 100%;
            margin-bottom: 15px;
        }

        .org-header td {
            border: none;
            vertical-align: middle;
        }

        .org-name {
            font-size: 16px;
            font-weight: bold;
        }

        .address {
            font-size: 13px;
            line-height: 1.4;
        }

        .amount-total {
            font-size: 15px;
            font-weight: bold;
        }

        .amount-number {
            font-size: 15px;
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>

<body>

    <h2>Resit Pembayaran Yuran</h2>

    <!-- Organization header -->
    <table class="org-header">
        <tr>
            <td style="width:20%; text-align:center;">
                @if(!empty($get_organization->organization_picture))
                @php
                $imageUrl = 'https://prim.my/organization-picture/' . $get_organization->organization_picture;
                $headers = @get_headers($imageUrl);
                $imageExists = $headers && strpos($headers[0], '200') !== false;
                @endphp
                @if($imageExists)
                <img src="{{ $imageUrl }}" height="80" alt="">
                @endif
                @endif
            </td>
            <td style="width:80%;">
                <div class="org-name">{{ $get_organization->nama }}</div>
                <div class="address">
                    {{ $get_organization->address }},
                    <br>{{ $get_organization->postcode }} {{ $get_organization->city }}, {{ $get_organization->state }}
                </div>
            </td>
        </tr>
    </table>

    <!-- Maklumat Resit -->
    <table>
        <tr>
            <th colspan="2" class="table-title">Maklumat Resit</th>
        </tr>
        <tr>
            <td style="width:30%;">No Resit</td>
            <td>{{ $get_transaction->description }}</td>
        </tr>
        <tr>
            <td>No Transaksi FPX</td>
            <td>{{ $get_transaction->transac_no ?? '-' }}</td>
        </tr>
        <tr>
            <td>Tarikh</td>
            <td>{{ $get_transaction->datetime_created->format('j M Y H:i:s A') }}</td>
        </tr>
    </table>

    <!-- Maklumat Pembayar -->
    <table>
        <tr>
            <th colspan="2" class="table-title">Maklumat Pembayar</th>
        </tr>
        <tr>
            <td style="width:30%;">Nama</td>
            <td>{{ $getparent->name }}</td>
        </tr>
        <tr>
            <td>No. Kad Pengenalan</td>
            <td>
                @if($getparent->icno)
                {{ $getparent->icno }}
                @else
                {{ $getparent->telno }}
                @endif
            </td>
        </tr>
    </table>

    <!-- Category A -->
    @if (count($getfees_categoryA) != 0)
    <div class="section-title">{{ $get_organization->nama }}</div>
    <div style="font-weight:bold; margin-bottom:5px;">Kategori A</div>
    <table>
        <tr>
            <th style="width:5%; text-align:center;">Bil.</th>
            <th>Item</th>
            <th style="width:20%; text-align:center;">Amaun (RM)</th>
        </tr>
        @foreach ($getfees_categoryA as $item)
        <tr>
            <td style="text-align:center;">{{ $loop->iteration }}</td>
            <td>{{ $item->name }} x {{ $item->quantity }}</td>
            <td style="text-align:center;">{{ number_format((float)$item->totalAmount, 2, '.', '') }}</td>
        </tr>
        @endforeach
        <tr>
            <td></td>
            <td style="text-align:right;"><strong>Jumlah</strong></td>
            <td style="text-align:center;"><strong>{{ number_format($getfees_categoryA->sum('totalAmount'), 2) }}</strong></td>
        </tr>
    </table>
    @endif

    <!-- Student & Categories -->
    @if (count($get_student) != 0)
    @foreach ($get_student as $student)
    <div class="section-title">{{ $student->nama }} ({{ $student->classname }})</div>
    @foreach ($get_category->where('studentid', $student->id) as $category)
    <div style="font-weight:bold; margin-bottom:5px;">{{ $category->category }}</div>
    <table>
        <tr>
            <th style="width:5%; text-align:center;">Bil.</th>
            <th>Item</th>
            <th style="width:20%; text-align:center;">Amaun (RM)</th>
        </tr>
        @foreach ($get_fees->where('studentid', $student->id)->where('category', $category->category) as $item)
        <tr>
            <td style="text-align:center;">{{ $loop->iteration }}</td>
            <td>{{ $item->name }} x {{ $item->quantity }}</td>
            <td style="text-align:center;">{{ number_format((float)$item->totalAmount, 2, '.', '') }}</td>
        </tr>
        @endforeach
        <tr>
            <td></td>
            <td style="text-align:right;"><strong>Jumlah</strong></td>
            <td style="text-align:center;"><strong>{{ number_format($get_fees->where('studentid', $student->id)->where('category', $category->category)->sum('totalAmount'), 2) }}</strong></td>
        </tr>
    </table>
    @endforeach
    @endforeach
    @endif

    <!-- Total Summary -->
    <table>
        <tr>
            <td style="border:none;"></td>
            <td colspan="3" style="text-align:right; border:none;">Caj yang dikenakan oleh organisasi (RM)</td>
            <td style="text-align:center; width:20%;">{{ number_format((float)$get_organization->fixed_charges, 2, '.', '') }}</td>
        </tr>
        <tr>
            <td style="border:none;"></td>
            <td colspan="3" class="amount-total" style="text-align:right; border:none;">Jumlah Bayaran (RM)</td>
            <td class="amount-number">{{ number_format((float)$get_transaction->amount, 2, '.', '') }}</td>
        </tr>
    </table>

</body>

</html>