
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    {{-- <title>PRIM</title> --}}
    <style>
        /* -------------------------------------
            GLOBAL
            A very basic CSS reset
        ------------------------------------- */
        * {
            margin: 0;
            padding: 0;
            font-family: "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;
            box-sizing: border-box;
            font-size: 14px;
        }

        img {
            max-width: 100%;
        }

        body {
            -webkit-font-smoothing: antialiased;
            -webkit-text-size-adjust: none;
            width: 100% !important;
            height: 100%;
            line-height: 1.6;
        }

        /* Let's make sure all tables have defaults */
        table td {
            vertical-align: top;
        }

        /* -------------------------------------
            BODY & CONTAINER
        ------------------------------------- */
        body {
            background-color: #f6f6f6;
        }

        .body-wrap {
            background-color: #f6f6f6;
            width: 100%;
        }

        .container {
            display: block !important;
            max-width: 600px !important;
            margin: 0 auto !important;
            /* makes it centered */
            clear: both !important;
        }

        .content {
            max-width: 600px;
            margin: 0 auto;
            display: block;
            padding: 20px;
        }

        /* -------------------------------------
            HEADER, FOOTER, MAIN
        ------------------------------------- */
        .main {
            background: #fff;
            border: 1px solid #e9e9e9;
            border-radius: 3px;
        }

        /* .content-wrap {
            padding: 20px;
        } */

        .content-block {
            padding: 30px 0 20px;
            text-align: center;
        }

        .header {
            width: 100%;
            margin-bottom: 20px;
        }

        .footer {
            width: 100%;
            clear: both;
            color: #999;
            padding: 20px;
        }
        .footer a {
            color: #999;
        }
        .footer p, .footer a, .footer unsubscribe, .footer td {
            font-size: 12px;
        }

        /* -------------------------------------
            TYPOGRAPHY
        ------------------------------------- */
        h1, h2, h3 {
            font-family: "Helvetica Neue", Helvetica, Arial, "Lucida Grande", sans-serif;
            color: #000;
            margin: 40px 0 0;
            line-height: 1.2;
            font-weight: 400;
        }

        h1 {
            font-size: 32px;
            font-weight: 500;
        }

        h2 {
            font-size: 24px;
        }

        h3 {
            font-size: 12px;
        }

        h4 {
            font-size: 14px;
            font-weight: 600;
        }

        p, ul, ol {
            margin-bottom: 10px;
            font-weight: normal;
        }
        p li, ul li, ol li {
            margin-left: 5px;
            list-style-position: inside;
        }

        /* -------------------------------------
            LINKS & BUTTONS
        ------------------------------------- */
        a {
            color: #1ab394;
            text-decoration: underline;
        }

        .btn-primary {
            text-decoration: none;
            color: #FFF;
            background-color: #1ab394;
            border: solid #1ab394;
            border-width: 5px 10px;
            line-height: 2;
            font-weight: bold;
            text-align: center;
            cursor: pointer;
            display: inline-block;
            border-radius: 5px;
            text-transform: capitalize;
        }

        /* -------------------------------------
            OTHER STYLES THAT MIGHT BE USEFUL
        ------------------------------------- */
        .last {
            margin-bottom: 0;
        }

        .first {
            margin-top: 0;
        }

        .aligncenter {
            text-align: center;
        }

        .alignright {
            text-align: right;
        }

        .alignleft {
            text-align: left;
        }

        .clear {
            clear: both;
        }

        /* -------------------------------------
            ALERTS
            Change the class depending on warning email, good email or bad email
        ------------------------------------- */
        .alert {
            font-size: 16px;
            color: #fff;
            font-weight: 500;
            padding: 20px;
            text-align: center;
            border-radius: 3px 3px 0 0;
        }
        .alert a {
            color: #fff;
            text-decoration: none;
            font-weight: 500;
            font-size: 16px;
        }
        .alert.alert-warning {
            background: #f8ac59;
        }
        .alert.alert-bad {
            background: #ed5565;
        }
        .alert.alert-good {
            background: #1ab394;
        }

        /* -------------------------------------
            INVOICE
            Styles for the billing table
        ------------------------------------- */
        .invoice {
            margin: auto auto 40px;
            text-align: left;
            width: 80%;
        }
        .invoice td {
            padding: 5px 0;
        }
        .invoice .invoice-items {
            width: 100%;
        }
        .invoice .invoice-items td {
            border-top: #eee 1px solid;
        }
        .invoice .invoice-items .total td {
            border-top: 2px solid #333;
            border-bottom: 2px solid #333;
            font-weight: 700;
        }

        /* -------------------------------------
            RESPONSIVE AND MOBILE FRIENDLY STYLES
        ------------------------------------- */
        @media only screen and (max-width: 640px) {
            h1, h2, h3, h4 {
                font-weight: 600 !important;
                margin: 20px 0 5px !important;
            }

            h1 {
                font-size: 22px !important;
            }

            h2 {
                font-size: 18px !important;
            }

            h3 {
                font-size: 16px !important;
            }

            .container {
                width: 100% !important;
            }

            .content, .content-wrap {
                padding: 10px !important;
            }

            .invoice {
                width: 100% !important;
                margin: auto !important;
            }
        }
    </style>
</head>
<body>
    <table class="body-wrap">
    <tbody><tr>
        <td></td>
        <td class="container" width="600">
            <div class="content">
                <table class="main" width="100%" cellpadding="0" cellspacing="0">
                    <tbody><tr>
                        <td class="content-wrap aligncenter">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tbody><tr>
                                    <td class="content-block" style="background-color:#626ed4;">
                                        <h1 style="color: #FFF">Peringatan Yuran</h1>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="content-block">
                                        <table class="invoice">
                                            <tbody>
                                                <tr>
                                                <td>
                                                    Dimaklumkan bahawa terdapat bayaran yuran yang belum diselesaikan pada pihak tuan / puan.<br><br>
                                                    <b>Pelajar :</b> {{ $debt->nama }}<br>
                                                    <b>Organisasi / Sekolah :</b> {{ $debt->o_nama }}<br>
                                                    <b>Kelas :</b> {{ $debt->c_nama }}<br>
                                                    <b>Tarikh Daftar Kelas :</b> {{ date('d-m-Y', strtotime($debt->cs_startdate)) }}<br>
                                                    <b>Yuran :</b> {{ $debt->fn_nama }}<br>
                                                    <b>Tempoh Yuran :</b> {{ date('d-m-Y', strtotime($debt->fn_startdate)) }} - {{ date('d-m-Y', strtotime($debt->fn_enddate)) }}<br>
                                                    <b>Jumlah Yang Perlu Dibayar :</b><p style="color:#e03e2d; display:inline;"> RM {{ number_format((float)$debt->fr_finalamount, 2, '.', '') }}</p><br><br><br>
                                                    <i style="color: #797979; margin-top: 50px;">Jika yuran telah dibayar, tetapi masih menerima email ini, sila beritahu admin organisasi / sekolah masing-masing.</i><br>
                                                </td>
                                                </tr>
                                            <tr>
                                                <td class="content-block" style="border-style:solid; background-color:#626ed4; color:#FFF;">
                                                    © Copyrights 2023 All rights reserved | PRiM
                                                </td>
                                            </tr>
                                        </tbody></table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        </td>
                    </tr>
                </tbody>
            </table>
            </div>
        </td>
        <td></td>
    </tr>
</tbody>
</table>
</body>
</html>
