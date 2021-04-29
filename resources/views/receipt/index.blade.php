@extends('layouts.master')

@section('css')
    <link href="{{ URL::asset('assets/css/receipt.css') }}" rel="stylesheet">
@endsection 

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
                                    <td class="content-block">
                                        <h2>Thanks for using our app</h2>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="content-block">
                                        <table class="invoice">
                                            <tbody><tr>
                                                <td>Anna Smith<br>Invoice #12345<br>June 01 2015</td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <table class="invoice-items" cellpadding="0" cellspacing="0">
                                                        <tbody><tr>
                                                            <td>Service 1</td>
                                                            <td class="alignright">$ 20.00</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Service 2</td>
                                                            <td class="alignright">$ 10.00</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Service 3</td>
                                                            <td class="alignright">$ 6.00</td>
                                                        </tr>
                                                        <tr class="total">
                                                            <td class="alignright" width="80%">Total</td>
                                                            <td class="alignright">$ 36.00</td>
                                                        </tr>
                                                    </tbody></table>
                                                </td>
                                            </tr>
                                        </tbody></table>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="content-block">
                                        <a href="#">View in browser</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="content-block">
                                        Company Inc. 123 Van Ness, San Francisco 94102
                                    </td>
                                </tr>
                            </tbody></table>
                        </td>
                    </tr>
                </tbody></table>
                {{-- <div class="footer">
                    <table width="100%">
                        <tbody><tr>
                            <td class="aligncenter content-block">Questions? Email <a href="mailto:">support@company.inc</a></td>
                        </tr>
                    </tbody></table>
                </div> --}}
            </div>
        </td>
        <td></td>
    </tr>
</tbody></table>