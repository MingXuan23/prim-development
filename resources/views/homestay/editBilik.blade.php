@extends('layouts.master')

@section('css')

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>


    <style>
        #img-preview{
            object-fit: cover;
            height: 150px;
            width: 200px;
            cursor: pointer;
        }
        
        @media screen and (max-width:500px){
            #image-previews{
                flex-wrap: nowrap!important;
                justify-content: flex-start!important;
                width:100%!important;
                overflow-x: auto;
            }
            .content-container{
                width: 100%!important;;
            }
            .card-body > div{
                display: block;
            }
            .card-body > div > .col-6{
               max-width: 100%;
            }
        }
    </style>
@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Edit Homestay/Bilik</h4>
        </div>
    </div>
</div>
<div class="content-container">
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

        @if(Session::has('success'))
            <div class="alert alert-success">
              <p>{{ Session::get('success') }}</p>
            </div>
          @elseif(Session::has('error'))
            <div class="alert alert-danger">
              <p>{{ Session::get('error') }}</p>
            </div>
          @endif
          <div class="flash-message"></div>

            <form method="post" action="{{route('homestay.updateRoom')}}" enctype="multipart/form-data"
                class="form-validation">
                {{csrf_field()}}
                <input type="hidden" name="roomid" value={{$room->roomid}}>
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-6">
                            <label class="control-label"> Nama Homestay atau Bilik <span style="color:#d00"> *</span> </label>
                            <input type="text" name="roomname" id="roomname" class="form-control" placeholder="Nama / Nombor Bilik"
                                data-parsley-required-message="Sila masukkan nama / nombor bilik" value="{{$room->roomname}}" required>
                        </div>
                        <div class="form-check col-6 d-flex align-items-center">
                            <input type="checkbox" name="isAvailable" id="isAvailable" {{ ($room->status == "Available") ? 'checked' : '' }} class="form-check-input">
                            <label for="isAvailable" class="form-check-label  mt-1">Dibuka Untuk Tempahan</label>
                        </div>
                    </div>
                    
                    <div class="row">
                        
                        <div class="col">
                            <div class="form-group required">
                                <label class="control-label">Kapasiti Homestay atau Bilik(pax) <span style="color:#d00"> *</span></label>
                                <input type="number"  class="form-control" id="roompax" name="roompax" value="{{$room->roompax}}" required>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group required">
                                <label class="control-label" for="price">Harga Per Malam (RM) <span style="color:#d00"> *</span></label>
                                <input type="text" min="1" class="form-control" id="price" name="price" value="{{$room->price}}" required>
                            </div>
                        </div>

                    </div>

                    <div class="row">
                            <div class="form-group col-6 ">
                                <label for="details">Detail Homestay atau Bilik<span style="color:#d00"> *</span></label>
                                <textarea rows="5" cols="30" name="details" id="details" class="form-control"  placeholder="Contoh : 2 Bilik 1 Bilik Air Wifi Disediakan Tempat Parking Banyak" required>{{$room->details}}</textarea>                                  
                            </div>
                            <div class="form-group col-6 ">
                                    <label for="address">Alamat Penuh <span style="color:#d00"> *</span></label>
                                    <textarea rows="5" cols="30" name="address" id="address" class="form-control"  placeholder="No.123, Hang Tuah Jaya, 76100 Durian Tunggal, Melaka" required>{{$room->address}}</textarea>                                  
                            </div>
                    </div>

                   {{-- <div class="form-group d-flex justify-content-center align-items-center gap-2">
                        <input type="file" name="images[]" id="images" multiple accept=".jpg,.jpeg,.png" class="form-control col-5" required>
                        <label for="images">Pilih gambar-gambar homestay/bilik(maximum 10 gambar)<span style="color:#d00"> *</span></label>
                   </div> --}}
                    <h3 class="text-center">Preview Images:</h3>
                    <div id="image-previews" class="d-flex justify-content-center align-items-center gap-1 flex-wrap mb-2">
                        @foreach($images as $image)
                            <div class="img-preview-container">
                                <img src="../{{$image->image_path}}"  class="img-thumbnail" id="img-preview">
                                <input type="file" name="image[]" id="{{$image->id}}" accept=".jpg,.jpeg,.png" hidden>
                            </div>
                        @endforeach
                    </div>

                    <div class="form-group mb-0">
                        <div class="text-right">
                            <a type="button" href="{{route('homestay.urusbilik')}}"
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

$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
        $('.alert').delay(3000).fadeOut()
        $('.img-thumbnail').on('click', function(){
            $(this).next('input')[0].click();
        });
        $('input[type="file"]').on('change', function(){
            const imagePreview = $(this).prev('img');
            const imageFile = $(this).prop('files')[0];
            if(imageFile && imageFile.type.includes('jpg')||imageFile.type.includes('jpeg')||imageFile.type.includes('png')){
                const image = new Image();
                image.src = URL.createObjectURL(imageFile);
                image.onload = function () {
                const maxWidth = 1280; // Maximum width allowed
                const maxHeight = 1280; // Maximum height allowed
                if (this.width > maxWidth || this.height > maxHeight) {
                    $('.flash-message').html(`
                        <div id="alert" class="alert alert-danger text-center">
                            Gambar  melebihi saiz maksimum yang dibenarkan (${maxWidth}x${maxHeight} piksel).
                        </div>
                    `);
                    $('#alert').fadeOut(6000);
                    $('#images').val('');
                } else {
                    // Display the image preview
                    imagePreview.attr('src',`${image.src}`);
                    } 
                }      
            }

        });
        // for image inputs
        $('#images').on('change', function(e){
            $('#image-previews').empty();
            const imageFiles = $(this).prop('files');
            if(imageFiles.length > 10){
                $('.flash-message').html(`
                    <div id="alert" class="alert alert-danger text-center">
                        Hanya dibenarkan muat naik maximum 10 gambar sahaja
                    </div>
                `);
                $('#alert').fadeOut(6000);
                $(this).val('');
                return;
            }else{
                for(let i = 0;i < imageFiles.length;i++){
                    if(imageFiles[i].type.includes('jpg')||imageFiles[i].type.includes('jpeg')||imageFiles[i].type.includes('png')){
                        const image = new Image();
                        image.src = URL.createObjectURL(imageFiles[i]);
                        image.onload = function () {
                        const maxWidth = 1280; // Maximum width allowed
                        const maxHeight = 1280; // Maximum height allowed
                        if (this.width > maxWidth || this.height > maxHeight) {
                            $('.flash-message').html(`
                                <div id="alert" class="alert alert-danger text-center">
                                    Gambar ke-${i + 1} melebihi saiz maksimum yang dibenarkan (${maxWidth}x${maxHeight} piksel).
                                </div>
                            `);
                            $('#alert').fadeOut(6000);
                            $('#images').val('');
                        } else {
                            // Display the image preview
                            $('#image-previews').append(`
                                <img src="${image.src}" class="img-thumbnail" id="img-preview">
                            `);
                            } 
                        }                 
                    }else{
                        $('.flash-message').html(`
                            <div id="alert" class="alert alert-danger text-center">
                                Hanya dibenarkan muat naik gambar sahaja
                            </div>
                        `);
                        $('#alert').fadeOut(6000);
                        $(this).val('');
                        return;
                    }
                }
            }
        });
});

</script>
@endsection