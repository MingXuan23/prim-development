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
            <form method="post" action="{{ route('organization.store') }} " enctype="multipart/form-data" class="form-validation">
                {{csrf_field()}}
                <div class="card-body">
                    <div class="form-group">
                        <label class="control-label">Nama Organisasi</label>
                        <input type="text" name="nama" class="form-control" placeholder="Nama Organisasi"
                        data-parsley-required-message="Sila masukkan nama organisasi" required>
                    </div>
                    <div class="form-group ">
                        <label class="control-label">No Telefon</label>
                        <input type="tel" name="telno" class="form-control phone_no" placeholder="No Telefon"
                        data-parsley-required-message="Sila masukkan no telefon" required>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Email</label>
                        <input type="text" name="email" class="form-control" placeholder="Email"
                        data-parsley-required-message="Sila masukkan email" required>
                    </div>
                    <div class="form-group ">
                        <label class="control-label">Jenis Organisasi</label>
                        <select name="type_org" id="type_org" class="form-control" 
                        data-parsley-required-message="Sila pilih jenis organisasi" required>
                            <option value="" selected>Semua Jenis Organisasi</option>
                            @foreach($type_org as $row)
                            <option value="{{ $row->id }}">{{ $row->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group ">
                        <label class="control-label">Cas Pembayaran (RM)</label>
                        <input id="input-currency" class="form-control input-mask text-left"
                            data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'placeholder': '0'"
                            im-insert="true" style="text-align: right;" name="fixed_charges" 
                            data-parsley-required-message="Sila masukkan cas pembayaran, masukkan 0 jika tidak mengenakan sebarang cas pembayaran" required>
                    </div>
                    <div class="row justify-content-center">
                        <div class="form-group col-md-8">
                            <label class="control-label">Alamat</label>
                            <textarea name="address" class="form-control" rows="4" placeholder="Alamat"
                            data-parsley-required-message="Sila masukkan alamat organisasi" required></textarea>
                        </div>
                        <div class="form-group col">
                            <label>Poskod</label>
                            <input type="text" name="postcode" class="form-control" placeholder="Poskod" 
                            data-parsley-required-message="Sila masukkan poskod" required>

                            <label>Negeri</label>
                            <input type="text" name="state" class="form-control" placeholder="Negeri"
                            data-parsley-required-message="Sila masukkan negeri" required>
                        </div>

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
    });
</script>
@endsection