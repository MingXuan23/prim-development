@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/css/checkbox.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/css/accordion.css') }}" rel="stylesheet" type="text/css" />

<style>
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
        background: #f1f1f1 ;
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
            <h4 class="font-size-18">Invois</h4>
        </div>
    </div>
</div>
<div class="card p-4">
    <div class="row">
        <div class="col-md-12 pb-3">
            <h3>{{ $getfees->nama  ?? '' }}</h3>
        </div>
        <div class="col-md-12 pb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Sila pilih item untuk dibayar</li>
            </ol>
        </div>
        <div class="col-md-12">
                <div class="row">
                    <div class="container-wrapper-scroll p-2 mb-3">
                        @foreach($getcat as $row)
                            <div class="col-md-12">
                                <div id="accordionExample{{ $row->cid }}" class="accordion shadow">
                                    <!-- Accordion item 1 -->
                                    <div class="card">
                                        <div id="heading{{ $row->cid }}"
                                            class="card-header bg-white shadow-sm border-0">
                                            <h6 class="mb-0 font-weight-bold"><a href="#" data-toggle="collapse"
                                                    data-target="#collapse{{ $row->cid }}" aria-expanded="true"
                                                    aria-controls="collapse{{ $row->cid }}"
                                                    class="d-block position-relative text-dark text-uppercase collapsible-link py-2">Kategori
                                                    {{ $row->cnama }}</a></h6>
                                        </div>
                                        <div id="collapse{{ $row->cid }}" aria-labelledby="heading{{ $row->cid }}"
                                            data-parent="#accordionExample{{ $row->cid }}" class="collapse show">
                                            <div class="card-body pl-0 pr-0">
                                                @foreach($getdetail->where('cid', $row->cid) as $row2)
                                                    <div class="inputGroup">
                                                        <input id="option{{ $row2->did }}" name="billcheck" value="{{ $row2->totalamount }}" onchange="checkD(this)" type="checkbox" />
                                                        <label for="option{{ $row2->did }}">
                                                            <span style="font-size: 18px">{{ $row2->dnama }}</span>
                                                            <br>
                                                            <span style="font-size: 14px;font-weight:100;">RM{{  number_format((float)$row2->totalamount, 2, '.', '') }} ({{ $row2->quantity }} kuantiti)</span>
                                                        </label>
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
                       <h4>Jumlah : RM<span id="pay"></span> </h4>
                    </div>
                    <div class="col-md-4 p-2">
                        <form method="POST" action="{{ route('fpxIndex') }}"
                        enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <input type="hidden" name="amount" id="amount" value="0.00">
                        <input type="hidden" name="o_id" id="o_id" value="{{ $getfees->id }}">
                        <button class="btn btn-success float-right" type="submit" onclick="">Bayar
                            Sekarang</button>
                    </form>
                    </div>
                        <input type="hidden" name="bname" id="bname" value="{{ $getfees->nama  ?? '' }}">
                        <input type="hidden" name="ttlpay" id="ttlpay" value="0.00">
                        <input type="hidden" value="{{ route('payment') }}" id="routepay">
                </div>
        </div>
    </div>
</div>


@endsection

@section('script')
<script>
    var amt = 0;

    $("#pay").html("0.00");

    function checkD(element) {
        var id = element.id;
        if (element.checked) {
            amt += parseFloat($("#" + id).val());
        } else {
            if(amt != 0)
            {
                amt -= parseFloat($("#" + id).val());
            }
        }   
        var total = parseFloat(amt).toFixed(2);
        $("#pay").html(total);
        $("input[name='amount']").val(total);

    }

    if ($('input[name="billcheck"]').not(':checked').length == 0) {
            $('input[name="checkall"]').prop("checked", true);
    }

</script>

    <script>

        var stripe = Stripe("pk_test_51I6AHSI3fJ2mpqjYMBKV0ioR1R1IA9rhHNtq2ILk4fgBfAItGOHeA0PL610VW67w55b2jHxa1tst80iuGDEarPMN00tXSQAxs7");
        var checkoutButton = document.getElementById("checkout-button");
        var linkpay = $("#routepay").val();

        checkoutButton.addEventListener("click", function () {

            var submitForm = new FormData();
            var allData = {
                "_token": "{{ csrf_token() }}",
                "bname": jQuery("#bname").val(),
                "ttlpay": jQuery("#ttlpay").val(), 
            }

            jQuery.each(allData, function(key, value) {
                submitForm.append(key, value);
            });

            jQuery.ajax({
                type: 'POST',
                url: linkpay,
                data: submitForm,
                processData: false,
                contentType: false,
                success: function(data) {
                    var obj = JSON.parse(data);
                    return stripe.redirectToCheckout({ sessionId: obj.id });
                    
                },
                error: function(data) {

                    console.error("Error:", data);
                }
            });
        });

        
    </script>
@endsection
