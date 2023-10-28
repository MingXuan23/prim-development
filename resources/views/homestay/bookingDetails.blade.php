@extends('layouts.master')

@section('css')
<link rel="stylesheet" href="{{ URL::asset('assets/homestay-assets/style.css')}}">
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
                            <div class="col-lg-2 col-sm-12 p-2 d-flex align-items-center">
                                    <img src="{{ asset($homestayImage) }}" class="img-fluid"
                                        alt="Homestay Image" />
                            </div>
                            <div class="col-lg-6 col-sm-12 p-2">
                                <h4>{{ $homestay->roomname }}</h4>
                                <p>{{ $homestay->address }}, {{ $homestay->area }},
                                    <br />
                                    {{ $homestay->postcode }}, {{$homestay->district}},{{ $homestay->state }}
                                </p>
                                <div>Organisasi: {{ $organization->nama }}</div>
                                <div>Tel No: {{$organization->telno}}</div>
                                <div>Email: {{$organization->email}}</div>
                            </div>
                            <div class="col-lg-4 table-responsive" style="width: 100%;">
                                <table>
                                    <tr style="background-color:#e9ecef;">
                                        <th colspan="6" class="text-center">Butiran</th>
                                    </tr>
                                    <tr>
                                        <td>No Resit</td>
                                        <td style="width: 50px">:</td>
                                        <td>{{ $transaction->description }}</td>
                                    </tr>
                                    <tr>
                                        <td>Tarikh</td>
                                        <td style="width: 50px">:</td>
                                        <td>{{  date('j M Y H:i:s A', strtotime($transaction->datetime_created))}}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-12 pt-3 table-responsive">
                                <table style="width:100%" class="infotbl ">
                                    <tr style="background-color:#e9ecef">
                                        <th colspan="9" class="text-center">Maklumat Pelanggan</th>
                                    </tr>
                                    <tr class="d-flex justify-content-center">
                                        <td class="py-3 mx-5">Nama: {{ $user->name }}</td>
                                        <td class="py-3 mx-5">Tel No.: {{ $user->telno }}</td>
                                    </tr>
                                    <tr style="background-color:#e9ecef">
                                        <th colspan="9" class="text-center">Maklumat Tempahan</th>
                                    </tr>
                                    {{-- <tr style="border-bottom:2px solid #e0e0e0">
                                        <td colspan="9" class="pt-2" style="font-size: 18px">
                                            Syuhaidi Bin Halim
                                        </td>
                                    </tr> --}}
                                </table>
                                
                                <div class="pt-2 pb-2">
                                    
                                </div>

                                <table class="table table-bordered table-striped" style="">
                                    <tr style="text-align: center">
                                        <th style="width:3%">Bil.</th>
                                        <th style="width:20%">Nama Homestay</th>
                                        <th style="width:25%">Daftar Masuk</th>
                                        <th style="width:25%">Daftar Keluar</th>
                                        <th style="width:10%">Bilangan Malam</th>
                                        <th style="width:10%">Amaun Semalam (RM)</th>
                                    </tr>
                                    {{-- @foreach ($booking_order as $item)
                                    <tr>
                                        <td style="text-align: center"> {{ $loop->iteration }}.</td>
                                        <td>
                                            <div class="pl-2"> {{ $item->nama }} </div>
                                        </td>
                                        <td style="text-align: center">{{ $item->roomname }}</td>
                                        <td style="text-align: center">{{ $item->checkin }}</td>
                                        <td style="text-align: center">{{ $item->checkout }}</td>
                                        <td style="text-align: center">{{  $item->price  }}</td>
                                    </tr>
                                    @endforeach --}}
                                    <tr>
                                        <td style="text-align: center">1</td>
                                        <td style="text-align: center">{{ $homestay->roomname }}</td>
                                        <td style="text-align: center">{{ date('d/m/Y', strtotime($transaction->checkin)) }}, selepas({{date('h:i a', strtotime($homestay->check_in_after))}})</td>
                                        <td style="text-align: center">{{ date('d/m/Y', strtotime($transaction->checkout)) }}, sebelum({{date('h:i a', strtotime($homestay->check_out_before))}})</td>
                                        <td style="text-align: center">{{  $numberOfNights }}</td>
                                        <td style="text-align: center">{{  $homestay->price  }}</td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td colspan="4" style="text-align:center"><b>Jumlah(termasuk diskaun dan penambahan harga semasa promosi)</b> </td>
                                        <td style="text-align:center">
                                            <b>{{ $transaction->totalprice  }}</b>

                                        </td>
                                    </tr>

                                </table>

                                <table style="width:100%" class="infotbl">
                                    <tr class="d-flex justify-content-end">
                                        <td colspan="3" style="text-align:right;font-size:18px;"><b>Jumlah Bayaran
                                                (RM)</b> </td>
                                        <td style="text-align:center; width:20%; font-size:18px">
                                            <b>{{  number_format((float)$transaction->amount, 2, '.', '') }}</b>
                                        </td>
                                    </tr>
                                </table>

                                <div class="d-flex justify-content-center">
                                    <a href="{{route('homestay.generateBookingDetailsPdf',$transaction->bookingid)}}" class="btn-purple">Muat Turun</a>
                                </div>
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

    });
  </script>
    
@endsection
