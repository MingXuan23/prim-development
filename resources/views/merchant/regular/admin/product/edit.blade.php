@extends('layouts.master')

@section('css')

<style>
#img-size
{
    width: 100px;
    height: 100px;
    object-fit: cover;
}

.vertical {
  border-left: 3px solid rgb(113, 113, 113);
  border-radius: 12px;
  height: auto;
}

@media only screen and (max-width: 600px) {
  .vertical {
    display: none;
  }
}

</style>

@endsection

@section('content')

<div class="row align-items-center">
  <div class="col-sm-6">
      <div class="page-title-box">
          <h4 class="font-size-18"></i>Kemaskini Produk Item</h4>
          <div class="form-group">
            <label><a href="{{ route('admin-reg.product-group') }}" class="text-muted">Urus Produk</a> <i class="fas fa-angle-right"></i> <a href="{{ route('admin-reg.product-item', $group->id) }}" class="text-muted">{{ $group->name }}</a> <i class="fas fa-angle-right"></i> Kemaskini - {{ $item->name }}</label>
          </div>
      </div>
  </div>
</div>

@if(Session::has('success'))
  <div class="alert alert-success">
    <p>{{ Session::get('success') }}</p>
  </div>
@elseif(Session::has('error'))
  <div class="alert alert-danger">
    <p>{{ Session::get('error') }}</p>
  </div>
@endif

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div class="row justify-content-center">
          <h3>{{ $item->name }}</h3>
        </div>
        

        <form action="{{ route('admin-reg.update-item') }}" method="POST" enctype="multipart/form-data" class="form-validation">
          {{csrf_field()}}
          @method('PUT')
          <div class="row">
            <div class="col">
              <div class="d-flex justify-content-center align-items-center mb-3">
                @if($item->image == null)
                <i>Tiada Imej</i>
                @else
                <img class="rounded img-fluid bg-dark" id="img-size" src="{{ URL($image_url.$item->image) }}">
                @endif
              </div>
                
              <div class="form-group required custom-file" style="margin-top: 9px">
                <label class="custom-file-label" for="item_image">Ubah Gambar Item</label>
                <input class="custom-file-input" type="file" name="item_image" id="item_image" accept=".jpg,.jpeg,.png">
              </div>

              <div class="row justify-content-center d-block m-3">
                  <h4>
                    {!! ($item->status == 1) 
                      ? "<span class='badge rounded-pill bg-success text-white d-block p-2'>Status Aktif</span>"
                      : "<span class='badge rounded-pill bg-danger text-white d-block p-2'>Status Tidak Aktif</span>" !!}
                  </h4>                
              </div>

              <div class="row justify-content-center d-block m-3">
                <h4>
                  {!! ($item->type == "have inventory") 
                    ? "<span class='badge rounded-pill bg-info text-white d-block p-2'>Ada Inventori</span>"
                    : "<span class='badge rounded-pill bg-info text-white d-block p-2'>Tiada Inventori</span>" !!}
                </h4>
              </div>
            </div>

            <div class = "vertical"></div>

            <div class="col-lg-9">
                <div class="row d-flex">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label>Nama</label>
                            <input type="text" name="item_name" value="{{ $item->name }}" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Harga Per Unit (RM)</label>
                            <input type="number" name="item_price" id="item_price" value="{{ number_format($item->price, 2) }}" min="0.01" step="0.01" pattern="^\d*(\.\d{1,2})?$" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label>Deskripsi</label>
                            <input type="text" name="item_desc" value="{{ $item->desc }}" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                  <div class="col">
                      <label class="col-form-label pt-0">Inventori</label>
                      <div class="col-sm-10">
                          <div class="form-check">
                              <input class="form-check-input" type="radio" name="inventory" id="no_inventory" value="no inventory">
                              <label class="form-check-label" for="no_inventory">
                                  Tiada
                              </label>
                          </div>
                          <div class="form-check">
                              <input class="form-check-input" type="radio" name="inventory" id="have_inventory" value="have inventory">
                              <label class="form-check-label" for="have_inventory">
                                  Ada
                              </label>
                          </div>
                      </div>
                  </div>

                  <div class="col">
                      <label class="col-form-label pt-0">Status</label>
                      <div class="col-sm-10">
                          <div class="form-check">
                              <input class="form-check-input" type="radio" name="status" id="active" value="1">
                              <label class="form-check-label" for="active">
                                  Aktif
                              </label>
                          </div>
                          <div class="form-check">
                              <input class="form-check-input" type="radio" name="status" id="inactive" value="0">
                              <label class="form-check-label" for="inactive">
                                  Tidak Aktif
                              </label>
                          </div>
                      </div>
                  </div>
                </div>

                <div class="row quantity-section" hidden>
                  <div class="col">
                      <div class="form-group">
                          <label>Kuantiti Inventori</label>
                          <input type="number" name="item_quantity" value="{{ $item->quantity_available }}" min="1" step="1" oninput="this.value = Math.round(this.value);" class="form-control">
                      </div>
                  </div>
                </div>

                <div class="row mb-4">
                  <div class="col">
                    <label class="control-label">Pakej</label>
                    <input class="form-control" type="number" placeholder="Kuantiti" name="selling_quantity" id="selling_quantity" min="1" step="1" oninput="this.value = Math.round(this.value);" value="{{ $item->selling_quantity }}" required>
                  </div>
                  <div class="col">
                      <label class="control-label">Nama Unit</label>
                      <input class="form-control" type="text" placeholder="Kata Nama Kuantiti" name="collective_noun" id="collective_noun" value="{{ $item->collective_noun }}" required>
                  </div>
                </div>
            </div>
          </div>

          <input type="hidden" name="org_id" value="{{ $group->organization_id }}">
          <input type="hidden" name="id" value="{{ $item->id }}">
          <input type="hidden" name="image_url" value="{{ $image_url }}">

          <div class="text-right">
            <a type="button" 
              href="{{ route('admin-reg.product-item', $item->product_group_id) }}"
              class="btn btn-secondary waves-effect waves-light mr-1">
              Kembali
            </a>
            <button type="submit" 
              class="btn btn-primary waves-effect waves-light">
              Kemaskini
            </button>
          </div>

        </form>

        <input type="hidden" name="old_type" id="old_type" value="{{ $item->type }}">
        <input type="hidden" name="old_status" id="old_status" value="{{ $item->status }}">

      </div>
    </div>
  </div>
</div>

@endsection

@section('script')

<script>
  $(document).ready(function(){
    $('.alert-success').delay(2000).fadeOut()

    displayQuantity()
    
    if($('#old_type').val() == "have inventory") {
      $('#have_inventory').prop('checked', true)
      displayQuantity()
    } else {
      $('#no_inventory').prop('checked', true)
      displayQuantity()
    }
    
    if($('#old_status').val() == 1) {
      $('#active').prop('checked', true)
    } else {
      $('#inactive').prop('checked', true)
    }

    function displayQuantity()
    {
      if($('#have_inventory').is(':checked')) {
        $('.quantity-section').prop('hidden', false)
        $("#item_quantity").prop('required',true)
      } else {
        $('.quantity-section').prop('hidden', true)
        $("#item_quantity").prop('required',false)
      }
    }

    $("#item_price").on('keydown', function(e){
        var input = $(this);
        var oldVal = input.val();
        var regex = new RegExp(input.attr('pattern'), 'g');

        setTimeout(function(){
            var newVal = input.val();
            if(!regex.test(newVal)){
              input.val(oldVal); 
            }
        }, 1);
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

    $('#have_inventory').click(function() {
        $('.quantity-section').prop('hidden', false)
        $("#item_quantity").prop('required',true)
    })

    $('#no_inventory').click(function() {
        $('.quantity-section').prop('hidden', true)
        $("#item_quantity").prop('required',false)
    })
        
  });
</script>

@endsection