@extends('layouts.master')

@section('css')
@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Organisasi</h4>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Organisasi >> Tambah Organisasi</li>
            </ol>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">

            @if(count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                    <li>{{$error}}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            {{-- {{ route('sekolah.store') }} --}}
            <form method="post" action="{{ route('organization.store') }} " enctype="multipart/form-data" class="form-validation outer-repeater">
                <div data-repeater-list="outer-group" class="outer">
                    <div data-repeater-item class="outer">
                    {{csrf_field()}}
                    <div class="card-body">

                        <select name="state" id="state" class="inner form-control"
                        data-parsley-required-message="Sila masukkan negeri" required>
                        <option value="">Pilih Negeri</option>
                            @for ($i = 0; $i < count($states); $i++)
                                <option value="{{ $states[$i]['id'] }}">{{ $states[$i]['name']}}</option>
                            @endfor
                        </select>
                        <br>
                        <div class="inner-repeater mb-4">
                            <div data-repeater-list="inner-group" class="inner form-group">
                                <div data-repeater-item class="inner mb-3 row">
                                    <div class="col-md-10 col-8">
                                        <div class="form-group">
                                            <label>Daerah</label>
                                                <select name="district" id="district" class="inner form-control district"
                                            data-parsley-required-message="Sila masukkan daerah" required>
                                            <option value="">Pilih Daerah</option>
                                        </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-4">
                                        <input data-repeater-delete type="button" class="btn btn-primary btn-block inner" value="Delete" />
                                    </div>

                                </div>
                            </div>
                            <input data-repeater-create type="button" class="btn btn-success inner" id="button_add" value="Add State" />
                        </div>

                        <div class="form-group mb-0">
                            <div class="text-right">
                                <a type="button" href="{{ url()->previous() }}" class="btn btn-secondary waves-effect waves-light mr-1">
                                    Kembali
                                </a>
                                <button type="submit" class="btn btn-primary waves-effect waves-light mr-1">
                                    Simpan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


@section('script')
<script src="{{ URL::asset('assets/libs/parsleyjs/parsleyjs.min.js')}}"></script>
<script src="{{ URL::asset('assets/libs/inputmask/inputmask.min.js')}}"></script>
<script src="{{ URL::asset('assets/libs/jquery-mask/jquery.mask.min.js')}}"></script>
<script src="{{ URL::asset('assets/libs/jquery-repeater/jquery-repeater.min.js')}}"></script>
<script src="{{ URL::asset('assets/js/pages/form-repeater.int.js')}}"></script>

<script>

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var state_id;
    var nextRowID = 0;

    function getDistrict (state_id){
        $.ajax({
                url: "{{ route('organization.get-district') }}",
                type: "POST",
                data: { 
                    state_id: state_id
                },
                success: function(data) {
                    // $('.district').empty();
                    var id = ++nextRowID;
                    for(var i = 0; i < data.length; i++){
                        data.sort();
                        $(".district").append("<option value='"+ i +"'>"+ data[i] +"</option>");
                    }
                }
            })
    }

    function getDistrict2 (state_id){
        $.ajax({
                url: "{{ route('organization.get-district') }}",
                type: "POST",
                data: { 
                    state_id: state_id
                },
                success: function(data) {
                    $('[data-repeater-item]').slice(1).remove();
                    $('#button_add').click();
                    
                    console.log('button click')
                    for(var i = 0; i < data.length; i++){
                        data.sort();
                        $('.district').empty();
                        $(".district").append("<option value='"+ i +"'>"+ data[i] +"</option>");
                    }
                }
            })
    }
        var timesChange = 0;

        $('#state').on('change', function(e) {
            timesChange++;
            console.log(timesChange);
            if (timesChange>1) {
                state_id = e.target.value;
                getDistrict2(state_id);
            } else {
                state_id = e.target.value;
                getDistrict(state_id);
            }
        });

</script>
@endsection