@extends('layouts.master')

@section('css')
<style>


    @media (max-width: 767px) {
       
      #buttonGroup {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
      #buttonGroup {
            flex-wrap: wrap;
        }

          #buttonGroup > * {
            flex-basis: 100%;
            margin-bottom: 10px;
        }

        #buttonGroup a:last-child {
            text-align: center;
        }
    }
</style>
@endsection

@section('content')

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
    <p id="success">{{ \Session::get('success') }}</p>
  </div>
@endif



<h4 class="font-size-18">Tambah Produk Type</h4>

<div class="card">
  <div class="card-body">
        
      <form action="{{route('koperasi.storeType')}}" method="POST" enctype="multipart/form-data">
        {!! csrf_field() !!}
        <label>* Nama Produk</label></br>
        <input type="text" name="name" id="name" class="form-control" placeholder="Masukkan Nama Produk"  value="" required></br>

        <div id="buttonGroup">
        <button type="submit" class="btn btn-primary waves-effect waves-light mr-1">Simpan</button>
       
  
                <a  href="#" class="btn btn-success" data-toggle="modal" data-target="#modelId"> <i
                    class="fas fa-plus" ></i> Import jenis produk</a>
        <a  href="{{route('koperasi.return',3)}}" class="btn btn-danger">Return</a>
        </div>
        
    </form>
  
  </div>
</div>
<br>
{{-- confirmation delete modal --}}
            <div id="deleteConfirmationModal" class="modal fade" role="dialog">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h4 class="modal-title">Padam Product Type</h4>
                  </div>
                  <div class="modal-body" id="modal-body-content">
                    
                  </div>
                  <div class="modal-footer">
                    
                    
                    @php
                    $groupId=0;
                    @endphp
                  <a href="#" style="display:inline"><button class="btn btn-primary mr-1">Padam</button>
                    <button type="button" data-dismiss="modal" class="btn">Batal</button></a>
                  </div>
                </div>
              </div>
            </div>
 
            {{-- end confirmation delete modal --}}
         
<div class="card">
  <div class="card-body">
  <div class="table-responsive">
          <table id="donationTable" class="table table-bordered table-striped dt-responsive"
            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
            <thead>
              <tr style="text-align:center">
                <th> No. </th>
                <th> Nama jenis produk </th>
                <th> Action </th>
              </tr>
            </thead>
            @foreach($group as $group)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $group->name }}</td>
                                        <td> 
                                        <a href="{{ route('koperasi.editType',$group->id) }}" style="display:inline"><button class="btn btn-primary mr-1">Edit</button>
                                          <a href="#" style="display:inline"> <button type="submit" class="btn btn-danger m-1" onclick="getProductNum({{$group->id}})">Padam</button>
                                      
                                      </td>
                                    </tr>
            @endforeach
</table>
  </div>
</div>
</div>

{{--import modal--}}
<div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Import Jenis Product</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('importproducttype') }}" method="post" enctype="multipart/form-data">
                        <div class="modal-body">

                            {{ csrf_field() }}

                            <div class="form-group">
                                <input type="file" name="file" required>
                            </div>
                            <input type="hidden" name="organ" id="organ" value="{{$org->id}}">

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Import</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>       
{{--end import modal--}}
@endsection

@section('script')


<script>
$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });


function getProductNum(groupId)
{

  $.ajax({
    url: '{{route("koperasi.getProductNumOfGroup")}}',
    type: 'POST',
    data: { groupId: groupId },
    success: function(response) {
      console.log('Success:', response.productNum);
      let productName=response.productNum;
      $("#modal-body-content").html("");
      let htmlText = "<p>";
      htmlText += "Total " + productName.length + " products will be affected</p>";
      for (let i = 0; (i < productName.length && i<5); i++) {
          htmlText += (i + 1) + ". " + productName[i].name + "<br>";
          if(i===4){
            htmlText += "<p>...some more items</p>";
          }
      }
      $("#modal-body-content").html(htmlText);
      let deleteUrl = '{{ route("koperasi.deleteType", ":groupId") }}';
      deleteUrl = deleteUrl.replace(':groupId', groupId);
      $("#deleteConfirmationModal a").attr('href', deleteUrl);
      
    },
    error: function(xhr, status, error) {
      console.log('error:'+error);
    }
  });
  
  $('#deleteConfirmationModal').modal('show');
}

$(document).on('click', '.btn-danger', function() {
  
});
</script>
@endsection