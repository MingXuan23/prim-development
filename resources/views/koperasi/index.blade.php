@extends('layouts.master')

@section('css')

@endsection

@section('content')

<div class="row align-items-center">
  <div class="col-sm-6">
      <div class="page-title-box">
          <h4 class="font-size-18">Koperasi</h4>
          <ol class="breadcrumb mb-0">
              <li class="breadcrumb-item active">Koperasi >> Pilih Koperasi</li>
          </ol>
      </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="card card-primary">
      <div class="card-body">
        @if(count($orgID) != 0)
        <div class="row">
          <div class="col">
            <div class="form-group required">
              <label class="control-label">Sekolah Anak Anda</label>
              <select name="sekolah_org" id="sekolah_org" class="form-control"
                data-parsley-required-message="Sila pilih Sekolah" required>
                <option value="" disabled selected>Pilih Sekolah</option>
                @foreach($orgID as $row)
                <option value="{{ $row->id }}">{{ $row->nama }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col">
            <div class="form-group required">
              <label class="control-label">Koperasi</label>
              <div id="koop"></div>
            </div>
          </div>
        </div>

        @else

        <section class="my-5">
          <div class="container-alt container">
              <div class="row justify-content-center">
                  <div class="col-10 text-center">
                      <div class="home-wrapper mt-5">
                          <div class="mb-4">
                              <img src="{{ URL::asset('assets/images/logo/prim-logo2.svg')}}" alt="logo" height="50" style="color: black" />
                          </div>

                          <h3 class="mt-4">Anda Tidak Memiliki Perhubungan dengan Mana-mana Sekolah</h3>
                          <p>Sila menghubungi pihak yang perlu jika terdapat sebarang masalah</p>
  
                      </div>
                  </div>
              </div>
          </div>
      </section>
        @endif
      </div>
    </div>
  </div>
</div>

@endsection

@section('script')

<script src="{{ URL::asset('assets/libs/peity/peity.min.js')}}"></script>

<script>
  $(document).ready(function(){
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    $('#sekolah_org').on('change', function(){

      if($(this).val() != '')
      {
        var sekolah_id = $(this).children(":selected").val();
        
        $.ajax({
          url: "{{ route('koperasi.fetchKoop') }}",
          method: "POST",
          data: {sID : sekolah_id},
          success:function(result)
          {
            $("#koop").removeAttr('style');
            $('#koop').empty();
            
            if(result.success.length == 0)
            {
              $('#koop').append("<p><i>Tiada Koperasi</i></p>");
            }
            else
            {
              $.each(result.success, function(key, value){
                $('#koop').append("<p>"+value.nama+"</p>");
                var url = '{{ route("koperasi.koopShop", ":id") }}';
                url = url.replace(':id', value.id);
                $('#koop').append("<a href='"+url+"') }}' class='btn btn-success waves-effect waves-light'>Pesan</a>");
              })
            } 
          },
          error:function(result)
          {
            console.log(result);
          }
          
        })
      }
      else
      {
        $('#koop').attr('style', 'display: none')
      }
    });
  }); 
   
</script>

@endsection