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
            <h3>Sila Pilih Sekolah Berkaitan Untuk Bayaran Yuran</h3>
        </div>
        {{-- <div class="col-md-12 pb-3">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Sila Pilih Sekolah</li>
            </ol>
        </div> --}}

        {{-- <div class="col-md-12">
            <div class="card card-primary">

                {{csrf_field()}}
        <div class="card-body">

            <div class="form-group">
                <label>Nama Organisasi</label>
                <select name="organization" id="organization" class="form-control">
                    <option value="" selected disabled>Pilih Organisasi</option>
                    @foreach($organization as $row)
                    <option value="{{ $row->id }}">{{ $row->nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div> --}}

<div class="col-md-12">
    <div class="row">
        <div class="container-wrapper-scroll p-2 mb-3">

            @foreach ($organization as $organizations)
            <div class="col-md-12">
                <div id="accordionExample{{ $organizations->id }}" class="accordion shadow">
                    <!-- Accordion item 1 -->
                    <div class="card">

                        <div class="inputGroup">
                            <input id="option-{{ $organizations->id }}" name="nameSchool"
                                value="{{ $organizations->id }}" type="checkbox" data-toggle="collapse"
                                data-target="#collapse{{ $organizations->id }}" aria-expanded="false"
                                aria-controls="collapse{{ $organizations->id }}"
                                class="d-block position-relative text-dark collapsible-link py-2"
                                onchange="checkOrganization(this)" />

                            <label for="option-{{ $organizations->id }}">
                                <span style="font-size: 18px">{{ $organizations->nama }}</span>
                                <br>
                            </label>
                        </div>

                        <div id="collapse{{ $organizations->id }}" aria-labelledby="heading{{ $organizations->id }}"
                            data-parent="#accordionExample{{ $organizations->id }}" class="collapse">
                            <div class="card-body pl-0 pr-0">

                                @foreach($getfees_category_A->where('organization_id', $organizations->id) as $data)
                                <div class="col-md-12">
                                    <div id="accordionExample{{ $organizations->id }}-{{ $organizations->user_id }}"
                                        class="accordion shadow">
                                        <!-- Accordion item 1 -->
                                        <div class="card">
                                            <div id="heading{{ $organizations->id }}-{{ $organizations->user_id }}"
                                                class="card-header bg-white shadow-sm border-0">
                                                <h6 class="mb-0 font-weight-bold"><a href="#" data-toggle="collapse"
                                                        data-target="#collapse{{ $organizations->id }}-{{ $organizations->user_id }}"
                                                        aria-expanded="true"
                                                        aria-controls="collapse{{ $organizations->id }}-{{ $organizations->user_id }}"
                                                        class="d-block position-relative text-dark text-uppercase collapsible-link py-2">
                                                        {{ $data->category }}</a></h6>

                                                <div id="collapse{{ $organizations->id }}-{{ $organizations->user_id }}"
                                                    aria-labelledby="heading{{ $organizations->id }}-{{ $organizations->user_id }}"
                                                    data-parent="#accordionExample{{ $organizations->id }}-{{ $organizations->user_id }}"
                                                    class="collapse show">
                                                    <div class="card-body pl-0 pr-0">
                                                        @foreach($getfees_category_A_byparent->where('organization_id', $organizations->id) as $item)
                                                        <div class="inputGroup">
                                                            <input
                                                                id="option-{{ $item->id }}-{{ $organizations->user_id }}"
                                                                name="billcheck" value="{{ $item->totalAmount }}"
                                                                onchange="checkD2(this)" type="checkbox" />

                                                            <label
                                                                for="option-{{ $item->id }}-{{ $organizations->user_id }}">
                                                                <span style="font-size: 18px">{{ $item->name }}</span>
                                                                <br>
                                                                <span
                                                                    style="font-size: 14px;font-weight:100;">RM{{  number_format((float)$item->totalAmount, 2, '.', '') }}
                                                                    ({{ $item->quantity }}
                                                                    kuantiti)</span>
                                                            </label>

                                                            {{-- hidden input checkbox second --}}
                                                            <input
                                                                id="option-{{ $item->id }}-{{ $organizations->user_id }}-2"
                                                                style="opacity: 0.0; position: absolute; left: -9999px"
                                                                checked="checked" name="billcheck2"
                                                                value="{{ $organizations->user_id }}-{{ $item->id }}"
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

                                @foreach($list->where('oid', $organizations->id) as $row)
                                <div class="col-md-12">
                                    <div id="accordionExample{{ $row->oid }}-{{ $row->studentid }}"
                                        class="accordion shadow">
                                        <!-- Accordion item 1 -->
                                        <div class="card">

                                            <div class="inputGroup">
                                                <input id="option-{{ $row->oid }}-{{ $row->studentid }}"
                                                    name="nameSchool" value="{{ $row->oid }}" type="checkbox"
                                                    data-toggle="collapse"
                                                    data-target="#collapse{{ $row->oid }}-{{ $row->studentid }}"
                                                    aria-expanded="false"
                                                    aria-controls="collapse{{ $row->oid }}-{{ $row->studentid }}"
                                                    class="d-block position-relative text-dark collapsible-link py-2" />

                                                <label for="option-{{ $row->oid }}-{{ $row->studentid }}">
                                                    <span style="font-size: 18px">{{ $loop->iteration }}.
                                                        {{ $row->studentname  }}</span>
                                                    <span> ( {{ $row->classname }} )</span>

                                                    <br>
                                                    <span> {{ $row->nschool }} </span>
                                                </label>
                                            </div>


                                            <div id="collapse{{ $row->oid }}-{{ $row->studentid }}"
                                                aria-labelledby="heading{{ $row->oid }}-{{ $row->studentid }}"
                                                data-parent="#accordionExample{{ $row->oid }}-{{ $row->studentid }}"
                                                class="collapse">
                                                <div class="card-body pl-0 pr-0">

                                                    @foreach($getfees->where('studentid', $row->studentid) as $data)
                                                    <div class="col-md-12">
                                                        <div id="accordionExample{{ $data->studentid }}-{{ $data->organization_id }}"
                                                            class="accordion shadow">
                                                            <!-- Accordion item 1 -->
                                                            <div class="card">
                                                                <div id="heading{{ $data->studentid }}-{{ $data->organization_id  }}"
                                                                    class="card-header bg-white shadow-sm border-0">
                                                                    <h6 class="mb-0 font-weight-bold"><a href="#"
                                                                            data-toggle="collapse"
                                                                            data-target="#collapse{{ $data->studentid }}-{{ $data->organization_id }}"
                                                                            aria-expanded="true"
                                                                            aria-controls="collapse{{ $data->studentid }}-{{ $data->organization_id  }}"
                                                                            class="d-block position-relative text-dark text-uppercase collapsible-link py-2">
                                                                            {{ $data->category }}</a></h6>

                                                                    <div id="collapse{{ $data->studentid }}-{{ $data->organization_id  }}"
                                                                        aria-labelledby="heading{{ $data->studentid }}-{{ $data->organization_id  }}"
                                                                        data-parent="#accordionExample{{ $data->studentid }}-{{ $data->organization_id  }}"
                                                                        class="collapse show">
                                                                        <div class="card-body pl-0 pr-0">
                                                                            @foreach($getfees_bystudent->where('studentid', $data->studentid)->where('category',$data->category) as $item)
                                                                            <div class="inputGroup">
                                                                                <input
                                                                                    id="option-{{ $item->id }}-{{ $data->studentid }}"
                                                                                    name="billcheck"
                                                                                    value="{{ $item->totalAmount }}"
                                                                                    onchange="checkD(this)"
                                                                                    type="checkbox" />

                                                                                <label for="option-{{ $item->id }}-{{ $data->studentid }}">
                                                                                    <span style="font-size: 18px">{{ $item->name }}</span>
                                                                                    <br>
                                                                                    <span style="font-size: 14px;font-weight:100;">RM{{  number_format((float)$item->totalAmount, 2, '.', '') }} ({{ $item->quantity }} kuantiti)</span>
                                                                                </label>

                                                                                {{-- hidden input checkbox second --}}
                                                                                <input
                                                                                    id="option-{{ $item->id }}-{{ $data->studentid }}-2"
                                                                                    style="opacity: 0.0; position: absolute; left: -9999px"
                                                                                    checked="checked" name="billcheck2"
                                                                                    value="{{$data->studentid}}-{{ $item->id }}"
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

        {{-- <input type="hidden" name="bname" id="bname" value="{{ $getfees->nama  ?? '' }}"> --}}
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
    $("#pay").html("0.00");
    var myCheckboxes = new Array();
    var myCheckboxes_categoryA = new Array();
    var organization_cb = new Array();
    var oid;

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
            console.log(myCheckboxes_categoryA);
            // console.log(myCheckboxes.length());
            // window.location.href = "{{ route('billIndex')}}";

            $.ajax({
                url: "{{ route('pay') }}",
                data: { 
                    id: myCheckboxes,
                    category: myCheckboxes_categoryA
                },
                
            })
            .done(function(response){
                document.write(response);
            });
            
        }
      });
    }); //Parameter
   
    ///*************** function for if different organization *****************
    function checkOrganization(element) {
        var id = document.getElementById(element.id);
        var name = document.getElementsByName(element.name);
        // console.log(name[0].value);
        oid = $('#'+element.id).val();
        // console.log(oid);

        // console.log(oid);
        if (id.checked) {
            for(var i=0; i < name.length; i++){
                
                if(name[i].checked ){

                    name[i].disabled = false;
                }else{
                    
                    if(oid == name[i].value){
                        name[i].disabled = false;
                    }
                    else{
                        name[i].disabled = true;
                    }
                
                }

            } 
        }else {
            for(var i=0; i < name.length; i++){
                name[i].disabled = false;
            } 

            total = 0;
            amt = 0;

            $("input[name='billcheck']:checkbox").prop('checked', false);
            $("input[name='billcheck2']:checkbox").prop('checked', false);
            $("#pay").html("0.00");
            myCheckboxes = [];
            myCheckboxes_categoryA = [];
            if(total == 0){
            document.getElementById('btn-byr').disabled = true;
            }else{
                document.getElementById('btn-byr').disabled = false;
            }
        } 
        
    }
    

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

    function checkD2(element) {
        var id = element.id;
        var id2 = element.id+"-2";
        if (element.checked) {
            amt += parseFloat($("#" + id).val());
            // $("#" + id2).prop('checked', true)

            $("#" + id2).prop('checked', true).each(function() {
                myCheckboxes_categoryA.push($(this).val());
            });
            
        } else {
            if(amt != 0)
            {
                amt -= parseFloat($("#" + id).val());
                $("#" + id2).prop('checked', false).each(function() {
                    myCheckboxes_categoryA = myCheckboxes_categoryA.filter(item => item !== $(this).val())
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

        // $('.getid').children().prop('disabled', true);

        // $('.getid').on("click",function(){
        //     var id =  $(this).attr("id");
        //     console.log(id);
        // var id = "#"+$(".getid").attr("id");

        //     $(".getid").children().prop('disabled', true);
        //     //post code
        // });

    // var elem = $('a[href="'+id+'"]');
    // elem.attr('disabled','disabled')

</script>

@endsection