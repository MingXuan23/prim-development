@extends('layouts.master')

@section('css')
<link rel="stylesheet" href="{{ URL::asset('assets/homestay-assets/style.css')}}">
<style>
    @media screen and (max-width: 700px){
        .row > .col-4{
            display: block;
            max-width: 100% !important;
        }
    }
</style>
@endsection

@section('content')
    <a href="{{url()->previous()}}" class="color-dark-purple" style="font-size: 20px;"><i class="mt-3 fas fa-chevron-left"></i>&nbsp;Kembali</a>
    <div class="mt-3" id="tab-container">
      <h3 class="color-purple text-center">Butiran Tempahan</h3>    
    </div>
    <div class="container border-white">
        <div class="row mt-3">
            <div class="col-12">
                <div class="card mb-1">
                    <div class="card-body py-5">
                        <div class="row">
                            <div class="col-4 p-2 d-flex align-items-center">
                                    <img src="{{ asset($homestayImage) }}" class="img-fluid"
                                        alt="Homestay Image" />
                            </div>
                            <div class="col-4 p-2">
                                <h4>{{ $homestay->roomname }}</h4>
                                <p>{{ $homestay->address }}, {{ $homestay->area }},
                                    <br />
                                    {{ $homestay->postcode }}, {{$homestay->district}},{{ $homestay->state }}
                                </p>
                                <div>Organisasi: {{ $organization->nama }}</div>
                                <div>Tel No: {{$organization->telno}}</div>
                                <div>Email: {{$organization->email}}</div>
                            </div>
                            <div class="col-4" >
                                <div style="background-color:#e9ecef;width: 100%" class="text-center">
                                    <b>Butiran</b>
                                </div>
                                <div>
                                    <b>No Resit: </b>{{ $transaction->description }}
                                </div>
                                <div>
                                    <b>Tarikh: </b>{{  date('j M Y H:i:s A', strtotime($transaction->datetime_created))}}
                                </div>
                            </div>
                        </div>
                            <div class="pt-3 text-center">
                                    <div style="background-color:#e9ecef">
                                        <b>Maklumat Pelanggan</b>
                                    </div>
                                    <div class="d-flex justify-content-center">
                                        <div class="py-3 mx-5"><b>Nama: </b> {{ $user->name }}</div>
                                        <div class="py-3 mx-5"><b>Tel No.: </b>{{ $user->telno }}</div>
                                    </div>
                                    <div style="background-color:#e9ecef">
                                        <b>Maklumat Tempahan</b>
                                    </div>
                                <div class="pt-2 pb-2">
                                    
                                </div>

                                <table class="table table-bordered table-striped table-responsive" style="">
                                    <tr style="text-align: center">
                                        <th style="width:3%">Bil.</th>
                                        <th style="width:20%">Nama Homestay</th>
                                        <th style="width:25%">Daftar Masuk</th>
                                        <th style="width:25%">Daftar Keluar</th>
                                        <th style="width:10%">Bilangan Malam</th>
                                        <th style="width:10%">Amaun Semalam (RM)</th>
                                    </tr>
                                    <tr>
                                        <td style="text-align: center">1</td>
                                        <td style="text-align: center">{{ $homestay->roomname }}</td>
                                        <td style="text-align: center">{{ date('d/m/Y', strtotime($transaction->checkin)) }}, selepas({{date('h:i a', strtotime($homestay->check_in_after))}})</td>
                                        <td style="text-align: center">{{ date('d/m/Y', strtotime($transaction->checkout)) }}, sebelum({{date('h:i a', strtotime($homestay->check_out_before))}})</td>
                                        <td style="text-align: center">{{  $numberOfNights }}</td>
                                        @if($transaction->booked_rooms == null)
                                            <td style="text-align: center">{{  $pricePerNight }}</td>
                                        @else
                                            <td style="text-align: center">{{  $pricePerNight }} <br> (x {{$transaction->booked_rooms}} Unit)</td>
                                        @endif
                                    </tr>
                                    @if($transaction->discount_received > 0)
                                        <tr>
                                            <td></td>
                                            <td colspan="4"><b>Diskaun</b></td>
                                            <td style="text-align: center">-{{  $transaction->discount_received}}</td>
                                        </tr>
                                    @endif
                                    @if($transaction->increase_received > 0)
                                        <tr>
                                            <td></td>
                                            <td colspan="4">Penambahan Harga</td>
                                            <td style="text-align: center">+{{  $transaction->discount_received}}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td></td>
                                        <td colspan="4" style="text-align:center"><b>Jumlah</b> </td>
                                        <td style="text-align:center">
                                            <b>{{ $transaction->totalprice  }}</b>

                                        </td>
                                    </tr>

                                </table>

                                <div class="d-flex justify-content-end align-items-center">
                                    <div class="mx-3" colspan="3" style="text-align:right;font-size:18px;"><b>Jumlah Bayaran
                                            (RM)</b> </div>
                                    <div style="font-size:18px;">
                                        <b>{{  number_format((float)$transaction->amount, 2, '.', '') }}</b>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-center mt-4">
                                    <button class="btn-purple" id="btn-download-receipt">Muat Turun</button>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
  {{-- sweet alert --}}
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
  <script>
    $(document).ready(function(){
        $('.navbar-header > div:first-child()').after(`
        <img src="../assets/homestay-assets/images/book-n-stay-logo(transparent).png" id="img-bns-logo">
        `);
        $('#btn-download-receipt').on('click', function(){
            window.print();
        })
    });
  </script>
    
@endsection
