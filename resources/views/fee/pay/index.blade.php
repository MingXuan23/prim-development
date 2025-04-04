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
        </div>
    </div>
</div>

<div class="card p-4">
    <div class="row">
        <div class="col-md-12 pb-3">
            <h3>Sila Pilih Sekolah Berkaitan Untuk Bayaran Yuran</h3>
        </div>

<div class="col-md-12">
    <div class="row">
        <div class="container-wrapper-scroll p-2 mb-3">

            @foreach ($organizations as $organization)
            <div class="col-md-12">
                <div id="accordionExample{{ $organization->id }}" class="accordion shadow">
                    <!-- Accordion item 1 -->
                    <div class="card">

                        <div class="inputGroup">
                            <input id="option-{{ $organization->id }}" name="nameSchool"
                                value="{{ $organization->id }}" type="checkbox" data-toggle="collapse"
                                data-target="#collapse{{ $organization->id }}" aria-expanded="false"
                                aria-controls="collapse{{ $organization->id }}"
                                class="d-block position-relative text-dark collapsible-link py-2"
                                onchange="checkOrganization(this)" />

                            <label for="option-{{ $organization->id }}">
                                <span style="font-size: 18px">{{ $organization->nama }}</span>
                                <br>
                            </label>
                        </div>

                        <div id="collapse{{ $organization->id }}" aria-labelledby="heading{{ $organization->id }}"
                            data-parent="#accordionExample{{ $organization->id }}" class="collapse">
                            <div class="card-body pl-0 pr-0">
                                {{-- category A --}}
                                @foreach($getfees_category_A->where('organization_id', $organization->id) as $data)
                                <div class="col-md-12">
                                    <div id="accordionExample{{ $organization->id }}-{{ $organization->user_id }}"
                                        class="accordion shadow">
                                        <!-- Accordion item 1 -->
                                        <div class="card">
                                            <div id="heading{{ $organization->id }}-{{ $organization->user_id }}"
                                                class="card-header bg-white shadow-sm border-0">
                                                <h6 class="mb-0 font-weight-bold"><a href="#" data-toggle="collapse"
                                                        data-target="#collapse{{ $organization->id }}-{{ $organization->user_id }}"
                                                        aria-expanded="true"
                                                        aria-controls="collapse{{ $organization->id }}-{{ $organization->user_id }}"
                                                        class="d-block position-relative text-dark text-uppercase collapsible-link py-2">
                                                        {{ $data->category }}</a></h6>

                                                <div id="collapse{{ $organization->id }}-{{ $organization->user_id }}"
                                                    aria-labelledby="heading{{ $organization->id }}-{{ $organization->user_id }}"
                                                    data-parent="#accordionExample{{ $organization->id }}-{{ $organization->user_id }}"
                                                    class="collapse show">
                                                    <div class="card-body pl-0 pr-0">
                                                        @foreach($getfees_category_A_byparent->where('organization_id', $organization->id) as $item)
                                                        <div class="inputGroup">
                                                            <input
                                                                id="option-{{ $item->id }}-{{ $organization->user_id }}"
                                                                name="billcheck" value="{{ $item->totalAmount }}"
                                                                onchange="checkD2(this)" type="checkbox" />

                                                            <label
                                                                for="option-{{ $item->id }}-{{ $organization->user_id }}">
                                                                <span style="font-size: 18px">{{ $item->name }}</span>
                                                                <br>
                                                                <span
                                                                    style="font-size: 14px;font-weight:100;">RM{{  number_format((float)$item->totalAmount, 2, '.', '') }}
                                                                    ({{ $item->quantity }}
                                                                    kuantiti)</span>
                                                            </label>

                                                            {{-- hidden input checkbox second --}}
                                                            <input
                                                                id="option-{{ $item->id }}-{{ $organization->user_id }}-2"
                                                                style="opacity: 0.0; position: absolute; left: -9999px"
                                                                checked="checked" name="billcheck2"
                                                                value="{{ $organization->user_id }}-{{ $item->id }}"
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

                                {{-- Kelas Name --}}
                                @foreach($list->where('oid', $organization->parent_org == null ? $organization->id : $organization->parent_org) as $row)
                                <div class="col-md-12">
                                    <div id="accordionExample{{ $organization->id }}-{{ $row->studentid }}"
                                        class="accordion shadow">
                                        <!-- Accordion item 1 -->
                                        <div class="card">
                                            <div class="inputGroup">
                                                @if($row->levelid!=0)
                                                    @if($row->type_org != 15)
                                                        <input id="option-{{ $organization->id }}-{{ $row->studentid }}"
                                                        name="nameSchool" value="{{ $organization->id }}" type="checkbox"
                                                        data-toggle="collapse"
                                                        data-target="#collapse{{ $organization->id }}-{{ $row->studentid }}"
                                                        aria-expanded="false"
                                                        aria-controls="collapse{{ $organization->id }}-{{ $row->studentid }}"
                                                        class="d-block position-relative text-dark collapsible-link py-2" />

                                                        <label for="option-{{ $organization->id }}-{{ $row->studentid }}">
                                                        <span style="font-size: 18px">{{ $loop->iteration }}.
                                                            {{ $row->studentname  }}</span>
                                                        <span> ( {{ $row->classname }} )</span>
                                                        <br>
                                                        <!-- <span> {{ $row->nschool }} </span> -->
                                                        </label>
                                                    @else
                                                        <input id="option-{{ $organization->id }}-{{ $row->studentid }}"
                                                        name="nameSchool" value="{{ $organization->id }}" type="checkbox"
                                                        data-toggle="collapse"
                                                        data-target="#collapse{{ $organization->id }}-{{ $row->studentid }}"
                                                        aria-expanded="false"
                                                        aria-controls="collapse{{ $organization->id }}-{{ $row->studentid }}"
                                                        class="d-block position-relative text-dark collapsible-link py-2" 
                                                        />

                                                        <label for="option-{{ $organization->id }}-{{ $row->studentid }}">
                                                        <span style="font-size: 18px">{{ $loop->iteration }}.
                                                            {{ $row->studentname  }}</span>
                                                        <span> ( {{ $row->classname }} | Tarikh Daftar: {{ date('d/m/Y', strtotime($row->student_startdate)) }} )</span>
                                                        <br>
                                                        <span> {{ $row->nschool }} </span>
                                                        <br>
                                                        <span></span>
                                                        </label>
                                                    @endif
                                                @else
                                                <label for="option-{{ $organization->id }}-{{ $row->studentid }}" style=" color:gray;">
                                                    <span style="font-size: 18px;" >{{ $loop->iteration }}.
                                                        {{ $row->studentname  }}</span>
                                                    <span> ( {{ $row->classname }} )</span>
                                                    <br>
                                                    <span> {{ $row->nschool }} </span>
                                                </label>
                                                @endif
                                            
                                               
                                                
                                            </div>
                                            @if($row->levelid!=0)
                                            <div id="collapse{{ $organization->id }}-{{ $row->studentid }}"
                                                aria-labelledby="heading{{ $organization->id }}-{{ $row->studentid }}"
                                                data-parent="#accordionExample{{ $organization->id }}-{{ $row->studentid }}"
                                                class="collapse student-collapse">
                                                <div class="card-body pl-0 pr-0">
                                                    {{-- Kelas Name --}}
                                                    @foreach($getfees->where('studentid', $row->studentid)->where('organization_id', $organization->id)->unique('category') as $data)
                                                    {{-- {{ dd($data) }} --}}
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

                                                                    <div id="collapse{{ $data->studentid }}-{{ $data->organization_id }}"
                                                                        aria-labelledby="heading{{ $data->studentid }}-{{ $data->organization_id  }}"
                                                                        data-parent="#accordionExample{{ $data->studentid }}-{{ $data->organization_id  }}"
                                                                        class="collapse show">
                                                                        <div class="card-body pl-0 pr-0">
                                                                            {{-- display item --}}
                                                                            @if($data->category != 'Kategori Berulang')
                                                                                @foreach($getfees_bystudent->where('studentid', $data->studentid)->where('category', $data->category)->where('organization_id', $organization->id) as $item)
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
                                                                            @else
                                                                                @foreach($getfees_bystudentSwasta->where('studentid', $data->studentid)->where('category', $data->category)->where('organization_id', $organization->id) as $item)
                                                                                <div class="inputGroup">
                                                                                    <input
                                                                                        id="option-{{ $item->feesnew_id }}-{{ $data->studentid }}"
                                                                                        name="billcheck"
                                                                                        value="{{ $item->finalAmount }}"
                                                                                        onchange="checkD(this)"
                                                                                        type="checkbox" />

                                                                                    <label for="option-{{ $item->feesnew_id }}-{{ $data->studentid }}">
                                                                                        <span style="font-size: 18px">{{ $item->name }}</span>
                                                                                        <br>
                                                                                        <span style="font-size: 14px;font-weight:100;">{{ date('d/m/Y', strtotime($item->start_date)) }} - {{ date('d/m/Y', strtotime($item->end_date)) }} ({{ $item->totalDay }} hari)</span>
                                                                                        <br>
                                                                                        @if($item->totalAmount == $item->finalAmount)
                                                                                            <span style="font-size: 14px;font-weight:100;">RM{{  number_format((float)$item->finalAmount, 2, '.', '') }} ({{ $item->quantity }} kuantiti)</span>
                                                                                        @else
                                                                                            <div class="icons d-inline">
                                                                                                <div class="fas fa-info-circle" 
                                                                                                    data-toggle="tooltip" 
                                                                                                    data-html=true
                                                                                                    data-original-title="Jumlah Asal = RM{{  number_format((float)$item->totalAmount / $item->totalDay, 2, '.', '') }}/hari, <br>
                                                                                                    Jumlah Terkini = RM{{  number_format((float)$item->finalAmount / $item->totalDay, 2, '.', '') }}/hari.">
                                                                                                </div>
                                                                                            </div>
                                                                                            <span class="d-inline" style="font-size: 14px;font-weight:100;">RM{{  number_format((float)$item->finalAmount, 2, '.', '') }} ({{ $item->quantity }} kuantiti)</span>
                                                                                        @endif
                                                                                        {{-- <span style="font-size: 14px;font-weight:100;">RM{{  number_format((float)$item->finalAmount, 2, '.', '') }} ({{ $item->quantity }} kuantiti)</span> --}}
                                                                                        {{-- <button type="button" class="btn btn-secondary" data-toggle="tooltip" data-placement="right" title="Tooltip on right">
                                                                                            i
                                                                                        </button> --}}
                                                                                    </label>

                                                                                    {{-- hidden input checkbox second --}}
                                                                                    <input
                                                                                        id="option-{{ $item->feesnew_id }}-{{ $data->studentid }}-2"
                                                                                        style="opacity: 0.0; position: absolute; left: -9999px"
                                                                                        checked="checked" name="billcheck2"
                                                                                        value="{{$data->studentid}}-{{ $item->feesnew_id }}"
                                                                                        type="checkbox" />
                                                                                </div>
                                                                                @endforeach
                                                                            @endif
                                                                            
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endforeach

                                                </div>
                                            </div>
                                            @endif
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

            <button id="btn-byr"  class="btn btn-success float-right" type="submit">Proses
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
            console.log(oid);
            // console.log(myCheckboxes.length());
            // window.location.href = "{{ route('billIndex')}}";

            $.ajax({
                url: "{{ route('pay') }}",
                data: { 
                    id: myCheckboxes,
                    category: myCheckboxes_categoryA,
                    org: oid
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

            //using id avoid get the data from other org
            $(id).closest('.card').find("input[name='billcheck']:checkbox").prop('checked', true).change();
            //$("input[name='billcheck']
            // $("input[name='billcheck2']:checkbox").prop('checked', true).change();
            $(id).closest('.card').find("input[value='" + oid + "']").closest('.card').find("input[name='nameSchool']").not(':checked').click();

            //$("input[name='billcheck2']:checkbox").prop('checked', true).change();
            
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

            $("input[name='billcheck']:checkbox").prop('checked', false).change();
            $("input[name='billcheck2']:checkbox").prop('checked', false).change();
            for(var i=0; i < name.length; i++){
                name[i].disabled = false;
            } 

            total = 0;
            amt = 0;

            
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
    $(document).ready(function() {
        // Bind event handler to the hidden.bs.collapse event of the collapse element
        $('.student-collapse').on('hidden.bs.collapse', function () {
            // Find all children checkboxes with the name "billcheck" and set their checked property to false
            $(this).children().find("input[name='billcheck']").prop('checked', false).change();
        });

        $('.student-collapse').on('shown.bs.collapse', function () {
        // Find all children checkboxes with the name "billcheck" and set their checked property to true
            $(this).children().find("input[name='billcheck']").not(':checked').prop('checked', true).change();
        });
    });

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