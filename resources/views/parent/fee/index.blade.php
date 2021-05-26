@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/libs/sweetalert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/css/checkbox.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/css/accordion.css') }}" rel="stylesheet" type="text/css" />

<style>
    body {
        /* overflow: hidden; */
    }

    .container-wrapper-scroll {
        width: 100%;
        height: 50vh;
        overflow-y: auto;
    }

    /* width */
    .container-wrapper-scroll::-webkit-scrollbar {
        width: 10px;
    }

    /* Track */
    .container-wrapper-scroll::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    /* Handle */
    .container-wrapper-scroll::-webkit-scrollbar-thumb {
        background: #888;
    }

    /* Handle on hover */
    .container-wrapper-scroll::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
</style>

@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Bayar Yuran</h4>
            <!-- <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Welcome to Veltrix Dashboard</li>
            </ol> -->
        </div>
    </div>
</div>

<div class="card p-4">
    <div class="row">
        <div class="col-md-12 pb-3">
            <h3> Senarai Nama Tanggungan</h3>
        </div>
        <div class="col-md-12 pb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Sila pilih item untuk dibayar</li>
            </ol>
        </div>
        <div class="col-md-12">
            <div class="row">
                <div class="container-wrapper-scroll p-2 mb-3">

                    @foreach($list as $row)
                    <div class="col-md-12">
                        <div id="accordionExample{{ $row->studentid }}" class="accordion shadow">
                            <!-- Accordion item 1 -->
                            <div class="card">
                                <div id="heading{{ $row->studentid }}" class="card-header bg-white shadow-sm border-0">
                                    <h4 class="mb-0 font-weight-bold"><a href="#" data-toggle="collapse"
                                            data-target="#collapse{{ $row->studentid }}" aria-expanded="false"
                                            aria-controls="collapse{{ $row->studentid }}"
                                            class="d-block position-relative text-dark collapsible-link py-2">
                                            <span style="text-transform:uppercase;"> {{ $loop->iteration }}.
                                                {{ $row->studentname }} </span>
                                            <br>
                                            <span> {{ $row->feename }} </span>
                                            <span> {{ $row->nschool }} </span>
                                        </a></h4>
                                </div>

                                <div id="collapse{{ $row->studentid }}" aria-labelledby="heading{{ $row->studentid }}"
                                    data-parent="#accordionExample{{ $row->studentid }}" class="collapse">
                                    <div class="card-body pl-0 pr-0">

                                        @foreach($getcat->where('feeid', $row->feeid) as $row1)
                                        <div class="col-md-12">
                                            <div id="accordionExample{{ $row->feeid }}-{{ $row1->cid }}"
                                                class="accordion shadow">
                                                <!-- Accordion item 1 -->
                                                <div class="card">
                                                    <div id="heading{{ $row->feeid }}-{{ $row1->cid }}"
                                                        class="card-header bg-white shadow-sm border-0">
                                                        <h6 class="mb-0 font-weight-bold"><a href="#"
                                                                data-toggle="collapse"
                                                                data-target="#collapse{{ $row->feeid }}-{{ $row1->cid }}"
                                                                aria-expanded="true"
                                                                aria-controls="collapse{{ $row->feeid }}-{{ $row1->cid }}"
                                                                class="d-block position-relative text-dark text-uppercase collapsible-link py-2">
                                                                {{ $row1->cnama }}</a></h6>

                                                        <div id="collapse{{ $row->feeid }}-{{ $row1->cid }}"
                                                            aria-labelledby="heading{{ $row->feeid }}-{{ $row1->cid }}"
                                                            data-parent="#accordionExample{{ $row->feeid }}-{{ $row1->cid }}"
                                                            class="collapse show">
                                                            <div class="card-body pl-0 pr-0">
                                                                @foreach($getdetail->where('cid',
                                                                $row1->cid)->where('feeid', $row->feeid) as $row2)
                                                                <div class="inputGroup">
                                                                    <input id="option-{{$row2->feeid}}-{{ $row2->did }}"
                                                                        name="billcheck"
                                                                        value="{{ $row2->totalamount }}"
                                                                        onchange="checkD(this)" type="checkbox" />

                                                                    <label
                                                                        for="option-{{$row2->feeid}}-{{ $row2->did }}">
                                                                        <span
                                                                            style="font-size: 18px">{{ $row2->dnama }}</span>
                                                                        <br>
                                                                        <span
                                                                            style="font-size: 14px;font-weight:100;">RM{{  number_format((float)$row2->totalamount, 2, '.', '') }}
                                                                            ({{ $row2->quantity }} kuantiti)</span>
                                                                    </label>

                                                                    {{-- hidden input --}}
                                                                    <input
                                                                        id="option-{{$row2->feeid}}-{{ $row2->did }}-2"
                                                                        style="opacity: 0.0; position: absolute; left: -9999px"
                                                                        checked="checked" name="billcheck2"
                                                                        value="{{$row->studentid}}-{{$row2->feeid}}-{{ $row2->did }}"
                                                                        type="checkbox" />
                                                                </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    @endforeach
                </div>
                <div class="col-md-8 p-3">
                    <h4>Jumlah Yang Perlu Dibayar : RM<span id="pay"></span> </h4>

                    {{-- <form method="POST" action="{{ route('fpxIndex') }}" enctype="multipart/form-data"> --}}

                </div>
                <div class="col-md-4 p-2">

                    <button id="btn-byr" disabled class="btn btn-success float-right" type="submit">Proses
                        Pembayaran</button>
                    {{-- </form> --}}
                </div>

                <input type="hidden" name="bname" id="bname" value="{{ $getfees->nama  ?? '' }}">
                <input type="hidden" name="ttlpay" id="ttlpay" value="0.00">
                <input type="hidden" value="{{ route('payment') }}" id="routepay">

            </div>
        </div>
    </div>
    <hr>

    {{-- <hr>
    <h4>FPX Terms & Conditions: <a href="https://www.mepsfpx.com.my/FPXMain/termsAndConditions.jsp" target="_blank">https://www.mepsfpx.com.my/FPXMain/termsAndConditions.jsp</a></h4> --}}
</div>
@endsection


@section('script')
<!-- Peity chart-->
<script src="{{ URL::asset('assets/libs/peity/peity.min.js')}}"></script>

<!-- Plugin Js-->
<script src="{{ URL::asset('assets/libs/chartist/chartist.min.js')}}"></script>

<script src="{{ URL::asset('assets/js/pages/dashboard.init.js')}}"></script>

<script src="{{ URL::asset('assets/libs/sweetalert2/sweetalert2.min.js')}}"></script>

<script>
    // return array like this 1-2-3
    // 1 = student id
    // 2 = fee id
    // 3 = details id

    var amt = 0;
    var total = 0;

    $('#btn-byr').click(function () {
      Swal.fire({
        title: "Adakah anda pasti?",
        text: "Jumlah yang perlu dibayar RM"+total,
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#34c38f",
        cancelButtonColor: "#f46a6a",
      }).then(function (result) {
        if (result.value) {
            console.log(myCheckboxes);
            // console.log(myCheckboxes.length());
            // window.location.href = "{{ route('billIndex')}}";

            $.ajax({
                url: "{{ route('feespay') }}",
                data: { id: myCheckboxes },
                
            })
            .done(function(response){
                document.write(response);
            });
            
        }
      });
    }); //Parameter
    
    $("#pay").html("0.00");
    var myCheckboxes = new Array();

    function checkD(element) {
        var id = element.id;
        var id2 = element.id+"-2";
        if (element.checked) {
            amt += parseFloat($("#" + id).val());
            // $("#" + id2).prop('checked', true)

            $("#" + id2).prop('checked', true).each(function() {
                myCheckboxes.push($(this).val());
            });

            // console.log(myCheckboxes);

        } else {
            if(amt != 0)
            {
                amt -= parseFloat($("#" + id).val());
                $("#" + id2).prop('checked', false).each(function() {
                    myCheckboxes = myCheckboxes.filter(item => item !== $(this).val())
                    // myCheckboxes.push($(this).val());
            });
            }
        }   
        total = parseFloat(amt).toFixed(2);
        $("#pay").html(total);
        $("input[name='amount']").val(total);

        if(total == 0){
            document.getElementById('btn-byr').disabled = true;
        }else{
            document.getElementById('btn-byr').disabled = false;
        }
    }

    // function checkD(element) {
    //     var id = element.id;
    //     if (element.checked) {
    //         amt += parseFloat($("#" + id).val());
    //     } else {
    //         if(amt != 0)
    //         {
    //             amt -= parseFloat($("#" + id).val());
    //         }
    //     }   
    //     total = parseFloat(amt).toFixed(2);
    //     $("#pay").html(total);
    //     $("input[name='amount']").val(total);

    //     if(total == 0){
    //         document.getElementById('btn-byr').disabled = true;
    //     }else{
    //         document.getElementById('btn-byr').disabled = false;
    //     }
    // }

    

    if ($('input[name="billcheck"]').not(':checked').length == 0) {
            $('input[name="checkall"]').prop("checked", true);
    }

</script>

@endsection