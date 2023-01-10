@extends('layouts.master')

@section('css')

<style>
#img-size
{
    width: 100px;
    height: 100px;
    object-fit: cover;
}
</style>

@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Dashboard Peniaga</h4>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Dashboard Peniaga >> Kemaskini Peniaga</li>
            </ol>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">
            @if(Session::has('success'))
            <div class="alert alert-success">
                <p>{{ Session::get('success') }}</p>
            </div>
            @elseif(Session::has('error'))
                <div class="alert alert-danger">
                    <p>{{ Session::get('error') }}</p>
                </div>
            @endif

            @if(count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                    <li>{{$error}}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            
            <form method="post" action="{{ route('admin-reg.update-merchant') }}" enctype="multipart/form-data" class="form-validation">
                @method('PUT')
                {{csrf_field()}}
                
                <div class="card-body">
                    <div class="form-group">
                        <input type="hidden" name="type_org" value="{{ $org->type_org }}">
                        <input type="hidden" name="id" value="{{ $org->id }}">
                        <label>Nama Organisasi</label>
                        <input type="text" name="nama" class="form-control" placeholder="Nama Organisasi"
                            value="{{ $org->nama }}" data-parsley-required-message="Sila masukkan nama organisasi" required>
                    </div>
                    
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label>No Telefon</label>
                                <input type="text" name="telno" class="form-control phone_no" placeholder="No Telefon"
                                    value="{{ $org->telno }}" data-parsley-required-message="Sila masukkan no telefon" required>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label>Email</label>
                                <input type="text" name="email" class="form-control" placeholder="Email"
                                    value="{{ $org->email }}" data-parsley-required-message="Sila masukkan email" required>
                            </div>
                        </div>
                    </div>                    

                    <div class="row mb-3">
                        <div class="col-sm-4">
                            <div class="form-group required custom-file" style="margin-top: 9px">
                                <label class="custom-file-label" for="organization_picture">Ubah Gambar Organisasi</label>
                                <input class="custom-file-input" type="file" name="organization_picture" id="organization_picture" accept=".jpg,.jpeg,.png">
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-start align-items-center mb-3">
                        @if($org->organization_picture == null)
                        <i>Tiada Imej</i>
                        @else
                        <img class="rounded img-fluid bg-dark" id="img-size" src="{{ URL('/organization-picture/'.$org->organization_picture) }}">
                        @endif
                    </div>
                    
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label>Cas Pembayaran (RM)</label>
                                <input id="input-currency" class="form-control input-mask text-left"
                                    data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'"
                                    im-insert="true" style="text-align: right;" name="fixed_charges"
                                    value="{{ $org->fixed_charges }}"
                                    data-parsley-required-message="Sila masukkan cas pembayaran, masukkan 0 jika tidak mengenakan sebarang cas pembayaran" required>
                            </div>
                        </div>

                        {{-- <div class="col">
                            <div class="form-group">
                                <label>Seller Id</label>
                                <input type="text" name="seller_id" class="form-control" placeholder="Seller Id Organisasi"
                                value="{{ $org->seller_id }}" data-parsley-required-message="Sila masukkan seller id organisasi" required>
                            </div>
                        </div> --}}
                    </div>
                    

                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                              <label>Negeri</label>
                              <select name="state" id="state" class="form-control"
                              data-parsley-required-message="Sila masukkan negeri" required>
                              <option value="">Pilih Negeri</option>
                                  @for ($i = 0; $i < count($states); $i++)
                                    @if(ucfirst(strtolower($states[$i]['name'])) == $org->state)
                                        <option id="{{ $states[$i]['id'] }}" value="{{ $org->state }}" selected> {{ $org->state }} </option>
                                    @else
                                        <option id="{{ $states[$i]['id'] }}" value="{{ ucfirst(strtolower($states[$i]['name'])) }}">{{ ucfirst(strtolower($states[$i]['name'])) }}</option>
                                    @endif
                                  @endfor
                              </select>
                            </div>
                          </div>
                        <div class="col">
                            <div class="form-group">
                                <label>Daerah</label>
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
                                    data-parsley-required-message="Sila masukkan poskod" value="{{ $org->postcode }}" required>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group required">
                                <label class="control-label">Bandar</label>
                                <input type="text" name="city" class="form-control" placeholder="Bandar"
                                    data-parsley-required-message="Sila masukkan bandar" value="{{ $org->city }}" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label">Alamat</label>
                        <textarea name="address" class="form-control" rows="4" placeholder="Alamat"
                        data-parsley-required-message="Sila masukkan alamat organisasi" required>{{ $org->address }}</textarea>
                    </div>

                    <div class="form-group mb-0">
                        <div class="text-right">
                            <a href="{{ route('admin-reg.home') }}" type="button" class="btn btn-light mr-2">Kembali</a>
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
    $(document).ready(function () {
        $('.form-validation').parsley();
        $(".input-mask").inputmask();
        $('.phone_no').mask('+600000000000');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(".custom-file-input").on("change", function() {
            var idxDot = this.value.lastIndexOf(".") + 1
            var extFile = this.value.substr(idxDot, this.value.length).toLowerCase()
            if (extFile=="jpg" || extFile=="jpeg" || extFile=="png"){
                var fileName = $(this).val().split("\\").pop()
                $(this).siblings(".custom-file-label").addClass("selected").html(fileName)
            }else{
                alert("Only jpg/jpeg and png files are allowed!")
                $(this).val('')
            }  
        })

        function toTitleCase(str) {
            var lcStr = str.toLowerCase();
            return lcStr.replace(/(?:^|\s)\w/g, function(match) {
                return match.toUpperCase();
            });
        }

        let state_id =  $('#state').children(":selected").attr("id");
        getDistrict(state_id);

        function getDistrict(){
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
                        if ("{{ $org->district }}" == district)
                            $("#district").append("<option value='"+ district +"' selected>"+ district +"</option>");
                        else
                            $("#district").append("<option value='"+ district +"'>"+ district +"</option>");
                    }
                }
            })
        }

        $('#state').on('change', function() {
            state_id = $(this).children(":selected").attr("id");
            getDistrict(state_id);
        });

        $('.alert').delay(3000).fadeOut();
    });
</script>
@endsection