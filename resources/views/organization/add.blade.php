@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/dropzone/dropzone.min.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ URL::asset('assets/css/required-asterick.css')}}" rel="stylesheet">
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

            @if(\Session::has('error'))
                <div class="alert alert-danger">
                    <p>{{ \Session::get('error') }}</p>
                </div>
            @endif
            {{-- {{ route('sekolah.store') }} --}}
            <form method="post" action="{{ route('organization.store') }} " enctype="multipart/form-data"
                class="form-validation">
                {{csrf_field()}}
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <div class="form-group required">
                                <label class="control-label">Nama Organisasi</label>
                                <input type="text" name="nama" class="form-control" placeholder="Nama Organisasi"
                                    data-parsley-required-message="Sila masukkan nama organisasi" required>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group required">
                                <label class="control-label">Jenis Organisasi</label>
                                <select name="type_org" id="type_org" class="form-control"
                                    data-parsley-required-message="Sila pilih jenis organisasi" required>
                                    <option value="" selected>Pilih Jenis Organisasi</option>
                                    @foreach($type_org as $row)
                                    <option value="{{ $row->id }}">{{ $row->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    

                    <div class="row">
                        <div class="col">
                            <div class="form-group required">
                                <label class="control-label">Email</label>
                                <input type="text" name="email" class="form-control" placeholder="Email"
                                    data-parsley-required-message="Sila masukkan email" required>
                            </div>
                        </div>

                        <div class="col">
                            <div class="form-group required">
                                <label class="control-label">No Telefon</label>
                                <input type="tel" name="telno" class="form-control phone_no" placeholder="No Telefon"
                                    data-parsley-required-message="Sila masukkan no telefon" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Logo Organisasi</label>
                        <form action="#" class="dropzone">
                            <div class="fallback">
                                <input name="organization_picture" type="file">
                            </div>
                        </form>
                    </div>

                    <div class="row">
                        <div class="col">
                            <div class="form-group required">
                                <label class="control-label">Negeri</label>
                                <select name="state" id="state" class="form-control"
                                    data-parsley-required-message="Sila masukkan negeri" required>
                                    <option value="">Pilih Negeri</option>
                                    @for ($i = 0; $i < count($states); $i++) <option id="{{ $states[$i]['id'] }}"
                                        value="{{ ucfirst(strtolower($states[$i]['name'])) }}">
                                        {{ ucfirst(strtolower($states[$i]['name'])) }}</option>
                                        @endfor
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group required">
                                <label class="control-label">Daerah</label>
                                <select name="district" id="district" class="form-control"
                                    data-parsley-required-message="Sila masukkan daerah" required>
                                    <option value="">Pilih Daerah</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <div class="form-group required">
                                <label class="control-label">Poskod</label>
                                <input type="text" name="postcode" class="form-control postcode" placeholder="Poskod"
                                    data-parsley-required-message="Sila masukkan poskod" required>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group required">
                                <label class="control-label">Bandar</label>
                                <input type="text" name="city" class="form-control" placeholder="Bandar"
                                    data-parsley-required-message="Sila masukkan bandar" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label"> Alamat <span style="color:#d00"> *</span> </label>
                        <textarea name="address" class="form-control" rows="4" placeholder="Alamat"
                            data-parsley-required-message="Sila masukkan alamat organisasi" required></textarea>
                    </div>

                    <div class="row" id="parent_org_class" style="display: none;">
                        <div class="col">
                            <div class="form-group required">
                                <label class="control-label">Sekolah</label>
                                <select name="parent_org" id="parent_org" class="form-control"
                                    data-parsley-required-message="Sila pilih jenis organisasi">
                                    <option value="" selected>Pilih Sekolah</option>
                                    {{-- @foreach($parent_org as $row)
                                    <option value="{{ $row->id }}">{{ $row->nama }}</option>
                                    @endforeach --}}
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group mb-0">
                        <div class="text-right">
                            <a type="button" href="{{ url()->previous() }}"
                                class="btn btn-secondary waves-effect waves-light mr-1">
                                Kembali
                            </a>
                            <button type="submit" class="btn btn-primary waves-effect waves-light mr-1">
                                Simpan
                            </button>
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

<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).ready(function () {
        $('.form-validation').parsley();
        $('.input-mask').inputmask();
        $('.phone_no').mask('+600000000000');
        $('.postcode').mask('99999');

        function toTitleCase(str) {
            var lcStr = str.toLowerCase();
            return lcStr.replace(/(?:^|\s)\w/g, function(match) {
                return match.toUpperCase();
            });
        }

        $('#state').on('change', function() {
            var state_id = $(this).children(":selected").attr("id");
            $.ajax({
                url: "{{ route('organization.get-district') }}",
                type: "POST",
                data: { 
                    state_id: state_id
                },
                success: function(data) {
                    $('#district').empty();
                    for(var i = 0; i < data.length; i++){
                        data.sort();
                        let district = toTitleCase(data[i]);
                        $("#district").append("<option value='"+ district +"'>"+ district +"</option>");
                    }
                }
            })
        });

        $('#type_org').on('change', function(){
            var type_org = $(this).children(":selected").text();
            if(type_org != "Koperasi")
            {
                $('#parent_org_class').attr('style', 'display: none;');
            }
        })
        
        $('#state').on('change', function(){
            
            var type_org = $('#type_org').children(":selected").text();
            var negeri = $(this).children(":selected").val();
            
            if(type_org == "Koperasi" && negeri != null)
            {
                $('#parent_org_class').removeAttr('style');
                $.ajax({
                    url: "{{ route('organization.fetchAvailableParentKoop') }}",
                    type: "POST",
                    data: {
                        negeri:negeri
                    },
                    success: function(data) {
                        var parent_org = $('#parent_org');
                        parent_org.empty();
                        parent_org.append("<option value='' disabled selected>Pilih Sekolah</option>")

                        $.each(data.success, function(index, value)
                        {
                            parent_org.append("<option value='"+value.id+"'>"+value.nama+"</option>")
                        })
                    },
                    error: function(data) {
                        console.log(data);
                    }
                })
            }
            else
            {
                $('#parent_org_class').attr('style', 'display: none;');
            }
            
        });

        $('.alert').delay(3000).fadeOut()
    });

</script>
@endsection