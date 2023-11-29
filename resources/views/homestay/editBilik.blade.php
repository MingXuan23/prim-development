@extends('layouts.master')

@section('css')

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
        <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <link rel="stylesheet" href="{{ URL::asset('assets/homestay-assets/style.css')}}">


@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18 page-title color-purple"><span><a href="{{route('homestay.urusbilik')}}" class="color-dark-purple">Urus Homestay >> </a></span>Edit Homestay</h4>
        </div>
    </div>
</div>
<div class="content-container">
    <div class="col-md-12 border-purple p-0">
        <div class="card card-primary mb-0">

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
                class="form-validation p-4" id="form-edit-homestay">
                {{csrf_field()}}
                <input type="hidden" name="roomid" value={{$room->roomid}}>
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-6">
                            <label class="control-label"> Nama Homestay <span style="color:#d00"> *</span> </label>
                            <input type="text" name="roomname" id="roomname" class="form-control" placeholder="Nama / Nombor Bilik"
                                data-parsley-required-message="Sila masukkan nama / nombor bilik" value="{{$room->roomname}}" required>
                        </div>
                        <div class="form-check col-6 d-flex align-items-center">
                            <input type="checkbox" name="isAvailable" id="isAvailable" {{ ($room->status == "Available") ? 'checked' : '' }} class="form-check-input">
                            <label for="isAvailable" class="form-check-label  mt-1">Dibuka Untuk Tempahan</label>
                        </div>
                    </div>
                    
                    <div class="row">
                        
                        <div class="col-md-3">
                            <div class="form-group required">
                                <label class="control-label">Kapasiti Homestay(pax) <span style="color:#d00"> *</span></label>
                                <input type="number"  class="form-control" id="roompax" name="roompax" value="{{$room->roompax}}" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group required">
                                <label class="control-label" for="price">Harga Per Malam (RM) <span style="color:#d00"> *</span></label>
                                <input type="text" min="1" class="form-control" id="price" name="price" value="{{$room->price}}" required>
                            </div>
                        </div>
                        <div class="col-md-3 form-group required d-none">
                            <div class="form-label"><b>Jenis Tempahan <span style="color:#d00"> *</span></b></div>
                            <label for="whole">Keseluruhan Homestay</label>
                            <input type="radio" name="bookingType" id="whole" value="whole" class="mr-2" {{$room->booking_type == "whole" ? 'checked' : ''}} hidden>
                            <label for="room">Bilik</label>
                            <input type="radio" name="bookingType" id="room" value="room" {{$room->booking_type == "room" ? 'checked' : ''}} hidden>
                        </div>
                        @if($room->booking_type == "room")
                            <div class="col-md-3 form-group required">
                                <label for="roomNo" class="form-label">Jumlah Unit</label>
                                <input type="number" min="1" name="roomNo" id="roomNo" class="form-control" value = "{{$room->room_no}}" required>
                            </div>
                        @endif
                    </div>

                    <div class="row">
                            <div class="form-group col-6">
                                <label for="details">Detail Homestay<span style="color:#d00"> *</span></label>
                                <textarea rows="10" cols="30" name="details" id="details" class="form-control"  placeholder="Contoh : 2 Bilik 1 Bilik Air Wifi Disediakan Tempat Parking Banyak" required>{{$room->details}}</textarea> 
                            </div>
                            <div class="form-group col-6 row mx-0 px-0">
                                <div class="form-group col-6 required mb-0">
                                    <label class="control-label">Negeri <span style="color:#d00"> *</span></label>
                                    <select name="state" id="state" class="form-select"
                                        data-parsley-required-message="Sila masukkan negeri" required>
                                        <option value="">Pilih Negeri</option>
                                        @for ($i = 0; $i < count($states); $i++) 
                                            <option id="{{ $states[$i]['id'] }}" {{ ucfirst(strtolower($states[$i]['name'])) == $room->state ? 'selected' : ''}}
                                            value="{{ ucfirst(strtolower($states[$i]['name'])) }}">
                                            {{ ucfirst(strtolower($states[$i]['name'])) }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-6">
                                    <div class="form-group required">
                                        <label class="control-label">Daerah <span style="color:#d00"> *</span></label>
                                        <select name="district" id="district" class="form-select"
                                            data-parsley-required-message="Sila masukkan daerah" data-original-district="{{$room->district}}" required>
                                            <option value="">Pilih Daerah</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <label for="area" class="form-label">Bandar <span style="color:#d00"> *</label>
                                    <input type="text" name="area" id="area" class="form-control" value="{{$room->area}}" required>
                                </div>
                                <div class="col-6">
                                    <label for="postcode" class="form-label">Poskod <span style="color:#d00"> *</label>
                                    <input type="text" name="postcode" id="postcode" class="form-control" value="{{$room->postcode}}" required>
                                </div>
                                <div>
                                    <label for="address">Nombor Rumah, Bangunan, Nama Jalan <span style="color:#d00"> *</span></label>
                                    <input type="text" name="address" id="address" class="form-control" placeholder="No.123, Taman Merdeka" value="{{$room->address}}" required></textarea>                                       
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="check-in-after">Daftar Masuk Selepas <span style="color:#d00"> *</span></label>    
                                        <input type="time" name="checkInAfter" id="check-in-after" class="form-control" value="{{$room->check_in_after}}" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="check-out-before">Daftar Keluar Sebelum <span style="color:#d00"> *</span></label>
                                        <input type="time" name="checkOutBefore" id="check-out-before" class="form-control" value="{{$room->check_out_before}}" required>
                                    </div>
                                </div>                        
                            </div>
                    </div>

                   {{-- <div class="form-group d-flex justify-content-center align-items-center gap-2">
                        <input type="file" name="images[]" id="images" multiple accept=".jpg,.jpeg,.png" class="form-control col-5" required>
                        <label for="images">Pilih gambar-gambar homestay/bilik(maximum 10 gambar)<span style="color:#d00"> *</span></label>
                   </div> --}}
                   <div class="image-flash-message"></div>
                    <h3 class="text-center">Preview Images:</h3>
                    <div id="image-previews" class="d-flex justify-content-center align-items-center gap-1 flex-wrap mb-2">
                        @foreach($images as $image)
                        <div>
                            <button id="btn-delete-image" type="button"><i class="fas fa-times"></i></button>
                            <img src="../{{$image->image_path}}"  class="img-thumbnail" id="img-preview">
                            <input type="file" name="image[]" id="{{$image->id}}" class="original_images" accept=".jpg,.jpeg,.png" hidden>                            
                        </div>
                       
                        @endforeach
                        <button id="btn-add-image" type="button"><i class="fas fa-plus"></i></button>
                        <input type="file"  name="new_images[]" id="new_images" accept=".jpg,.jpeg,.png" multiple hidden>

                    </div>

                    <input type="hidden" name="delete_id">
                    <input type="hidden" name="edit_id">
                    
                    <div class="form-group mb-2">
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
    // function toggleRoomNoInput() {
    //         if ($('#room').is(':checked')) {
    //             $('#roomNo').prop('disabled', false);
    //         } else {
    //             $('#roomNo').prop('disabled', true);
    //             $('#roomNo').val('');
    //         }
    //     }

    //     // Initial state check
    //     toggleRoomNoInput();

    //     // Event listeners for radio buttons
    //     $('#room, #whole').on('change', function() {
    //         toggleRoomNoInput();
    //     });
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
                // const maxWidth = 1280; // Maximum width allowed
                // const maxHeight = 1280; // Maximum height allowed
                // if (this.width > maxWidth || this.height > maxHeight) {
                //     $('.image-flash-message').html(`
                //         <div id="alert" class="alert alert-danger text-center">
                //             Gambar  melebihi saiz maksimum yang dibenarkan (${maxWidth}x${maxHeight} piksel).
                //         </div>
                //     `);
                //     $('#alert').fadeOut(6000);
                //     $('#images').val('');
                // } else {
                    // Display the image preview
                    imagePreview.attr('src',`${image.src}`);
                    imagePreview.addClass('img-new');

                    } 
                // }      
            }
            const imageId = $(this).attr('id');
            $('input[name="edit_id"]').val(function(index, value){
                return (value ? value + ',' : '') + imageId;
            });

        });
        // for image inputs

        $('#images').on('change', function(e){
            $('#image-previews').empty();
            const imageFiles = $(this).prop('files');
            if(imageFiles.length > 20){
                $('.flash-message').html(`
                    <div id="alert" class="alert alert-danger text-center">
                        Hanya dibenarkan muat naik maximum 20 gambar sahaja
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
                        // const maxWidth = 1280; // Maximum width allowed
                        // const maxHeight = 1280; // Maximum height allowed
                        // if (this.width > maxWidth || this.height > maxHeight) {
                        //     $('.image-flash-message').html(`
                        //         <div id="alert" class="alert alert-danger text-center">
                        //             Gambar ke-${i + 1} melebihi saiz maksimum yang dibenarkan (${maxWidth}x${maxHeight} piksel).
                        //         </div>
                        //     `);
                        //     $('#alert').fadeOut(6000);
                        //     $('#images').val('');
                        // } else {
                            // Display the image preview
                            $('#image-previews').append(`
                                <img src="${image.src}" class="img-thumbnail img-new" id="img-preview">
                            `);
                            } 
                        // }                 
                    }else{
                        $('.image-flash-message').html(`
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
        // insert new images
        $('#btn-add-image').on('click', function(){
            $('#new_images').click();
        });
        $('#new_images').on('change',function(e){
            // reset new image previews
            $('#image-previews > div ~ img').remove();
            const imageFiles = $(this).prop('files');
            // get the remaining number of images allowed to be uploaded
            const remainingNumber= 20 - Object.keys($('.original_images')).length;
            if(imageFiles.length > remainingNumber){
                $('.image-flash-message').html(`
                    <div id="alert" class="alert alert-danger text-center">
                        Hanya dibenarkan muat naik maximum 20 gambar sahaja
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
                        // const maxWidth = 1280; // Maximum width allowed
                        // const maxHeight = 1280; // Maximum height allowed
                        // if (this.width > maxWidth || this.height > maxHeight) {
                        //     $('.image-flash-message').html(`
                        //         <div id="alert" class="alert alert-danger text-center">
                        //             Gambar ke-${i + 1} melebihi saiz maksimum yang dibenarkan (${maxWidth}x${maxHeight} piksel).
                        //         </div>
                        //     `);
                        //     $('#alert').fadeOut(6000);
                        //     $('#images').val('');
                        // } else {
                            // Display the image preview
                            const images = `
                                <img src="${image.src}" class="img-thumbnail img-new" id="img-preview">
                            `;
                            // insert after the last original image preview
                            $(images).insertAfter($('#image-previews > div')[$('#image-previews > div').length- 1]);
                            // } 
                        }                 
                    }else{
                        $('.image-flash-message').html(`
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
        // for deleting images
        $(document).on('click', '#btn-delete-image',function(){
            $(this).next('#img-preview').remove();
            const imageId = $(this).next().attr('id');
            $(this).remove();
            $('input[name="delete_id"]').val(function(index, value){
                return (value ? value + ',' : '') + imageId;
            });
        });
        $('#form-edit-homestay').on('submit',function(e){
            if($('#image-previews img').length < 5){
                e.preventDefault();
                $('.image-flash-message').html(`
                    <div id="alert" class="alert alert-danger text-center">
                        Sila muat naik sekurang-kurangnya 5 gambar
                    </div>
                `);
                $('#alert').fadeOut(6000);
                $(this).val('');
                return;
            }
        })
    // to fetch district options based on selected state
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
                    const originalDistrict = $('#district').attr('data-original-district');
                    $('#district').empty();
                    for(var i = 0; i < data.length; i++){
                        data.sort();
                        let district = toTitleCase(data[i]);
                        if(district == originalDistrict){
                            $("#district").append("<option selected value='"+ district +"'>"+ district +"</option>");
                        }else{
                            $("#district").append("<option value='"+ district +"'>"+ district +"</option>");
                        }
                    }
                }
            })
        });

    $('#state').trigger('change');


});

</script>
@endsection