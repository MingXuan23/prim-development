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
                console.log(value);
                let htmlText='<div class="row">' +
                    '<div class="col-12">' +
                        '<h4 class="my-3"></h4>' +
                        '<div class="card-group">' +
                            '<div class="card mb-4">' ;
                if(value.organization_picture==null){
                  let imageUrl = "{{ URL('images/koperasi/default-item.png') }}";
                    htmlText+='<div class="col-lg-4">' +
                                    '<div class="card text-white bg-dark">' +
                                        '<div class="card-body">' +
                                        '<blockquote class="card-blockquote mb-0">' +
                                        '<img class="card-img-top img-fluid" src="'+imageUrl+'" alt="Card image cap">' +
                                            '</blockquote>' +
                                            '</div>' +
                                    '</div>' +
                                '</div>';
                }else{
                    let imageUrl = "{{ URL::asset('/organization-picture/') }}" +"/"+ value.organization_picture;
                    console.log(imageUrl);
                    htmlText+='<img class="card-img-top img-fluid" src="'+imageUrl+'" alt="Card image cap" style="height: 300px;">' ;
                }
                htmlText+='<div class="card-body">' +
                                    '<h4 class="card-title">'+value.nama+'</h4>';
                htmlText+='<p class="card-text">' +
                              '<small class="text-muted"><i class="fas fa-map-marker-alt mr-2"></i>'+ value.address+','+ value.city+','+value.state+'</small>' ;
                     htmlText+='<small>' +
                          '<div class="d-flex">';
                        if(value.status!=0){
                          var k_open_hour = new Date('value.open_hour').toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
                          var k_close_hour = new Date('value.close_hour').toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });

                          htmlText+='<p class="mr-4"><b>Waktu Buka</b></p>' +
                                                    '<p>Hari ini' +k_open_hour +'-'+k_close_hour +'</p>' ;
                        }else{
                          htmlText+='<p><b>Tutup pada hari ini</b></p>' ;
                        }
                          htmlText+= '</div>' +
                                        '</small>' +
                                    '</p>';
                                
                    
                //$('#koop').append(htmlText);
                var url = '{{ route("koperasi.koopShop", ":id") }}';
                url = url.replace(':id', value.id);
                $('#koop').append(htmlText+"<a href='"+url+"') }}' class='btn btn-success waves-effect waves-light'>Pesan</a></div></div></div></div></div>");
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