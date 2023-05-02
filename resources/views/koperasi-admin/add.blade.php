@extends('layouts.master')

@section('css')

@endsection

@section('content')
<h4 class="font-size-18">Tambah Produk</h4>

<div class="card">
  <div class="card-body">
      @if(count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                <li>{{$error}}</li>
                @endforeach
            </ul>
        </div>
        @endif

      <form action="{{ route('koperasi.storeProduct') }}" method="POST" enctype="multipart/form-data">
        {!! csrf_field() !!}
        
        <label>* Nama</label></br>
        <input type="text" name="nama" id="nama" class="form-control" placeholder="Masukkan Nama Produk"  value="" required></br>
        <label>Penerangan</label></br>
        <input type="text" name="description"  id="description" class="form-control"placeholder="Penerangan Produk"></br>
        <label>* Harga</label></br>
        <input type="number" name="price" id="price" value="" step="any"
        class="form-control" placeholder="0.00" required  min="0"></br>
        <label>* Kuantiti</label></br>
        <input type="number" name="quantity" id="quantity" class="form-control" placeholder="Masukkan bilangan kuantiti Produk"  value="" required min="0"></br>
        <label>* Jenis Produk</label></br>

        <div class="col">
                            <div class="form-group required">
                                <select name="type" id="type" class="form-control"
                                    data-parsley-required-message="Sila masukkan jenis produk" required>
                                    @foreach($type as $row)
                                     <option value="{{ $row->id }}">{{ $row->name }}</option>
                                     @endforeach
                                </select>
                            </div>
        </div>

        <label>Tahun</label>
        <div class="col">
           
            <select name="year" id="year" class="form-control">
                <option value="All" selected>Semua Tahun</option>
            </select>
            <div class="cbhide">

            </div>
        </div>
  
        <label>Status Produk</label></br>
      
        <div class="col">
                            <div class="form-group required">
                                <select name="status" class="form-control"
                                    data-parsley-required-message="Sila masukkan jenis produk" required>
                                    <option value="0">not available</option>
                                    <option value="1"> available</option>
                                </select>
                            </div>
        </div>

         <label>Gambar Produk</label>
         <div class="fallback">
         <input name="image" type="file" id='image'></br></br>
        </div>
        

        <button type="submit" class="btn btn-primary waves-effect waves-light mr-1">Simpan</button>
        <a  href="{{route('koperasi.return',1)}}" class="btn btn-danger">Return</a>
    </form>
  
  </div>
</div>

@endsection

@section('script')
<script>
$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

$(document).ready(function()
{
    $.ajax({
               url: "{{ route('koperasi.fetchClassYear') }}",
               method: "GET",
               success: function(result) {
                            jQuery.each(result.datayear, function(key, value) {
                                $("#year").append("<option value='"+ value.year +"'> Tahun " + value.year + "</option>");
                                let htmlContent="<div id='Tahun"+value.year+"' class='form-check-inline pb-3 pt-3'>";

                                htmlContent+="<label for='form-check-label' style='margin-right: 22px;' class='form-check-label'>";
                                htmlContent+="<input class='yearCheckbox' data-parsley-required-message='Sila pilih tahun' data-parsley-errors-container='.errorMessageCB' type='checkbox' id='cb_year' name='cb_year[]' value='" +
                                    value.year + "'/> " +"Tahun " +value.year + "</label>";
                                const filteredClass = result.classes.filter(c => c.nama.startsWith(value.year));
                                console.log(filteredClass);
                                jQuery.each(filteredClass, function(key, value) {
                                    htmlContent+="<label for='form-check-label' style='margin-right: 22px;' class='form-check-label cb_class'> <input class='classCheckbox' data-parsley-required-message='Sila pilih tahun' data-parsley-errors-container='.errorMessageCB' type='checkbox' id='cb_class' name='cb_class[]' value='" +
                                        value.id + "'/> " +value.nama + " </label>"
                                //console.log(filteredClass)
                                
                            });  
                            htmlContent+="</div><br>";
                            $(".cbhide").append(htmlContent);
                            $(".cbhide").hide();
                            $('.cb_class').hide();

   
                            $('.yearCheckbox').change(function(){
                                const classDiv = $('#Tahun'+$(this).val());
                                const classCheckBox = classDiv.find('input[type=checkbox]');
                                const classDivComponent = classDiv.find('.cb_class');
                                if($(this).is(':checked')){
                                    classCheckBox.prop('checked', true);
                                    classDivComponent.show();
                                } else {
                                    classCheckBox.prop('checked', false);
                                    classDivComponent.hide();
                                    
                                } 


                            });

                            $('.classCheckbox').change(function(){
                                var DivId = $(this).closest('div').attr('id');
                                const classDiv = $('#'+DivId);
                                const classCheckBox = classDiv.find('input[type=checkbox]');
                                const selectedCheckBox=classCheckBox.filter(':checked');

                                if(selectedCheckBox.length==1){
                                    classDiv.find('input[type=checkbox]').prop('checked', false);
                                    checkYearCheckBox();
                                }
                                console.log("Select:"+selectedCheckBox.length);
                                console.log("All :"+classCheckBox.length)
                            });
                                                        
                            })
                        }
                });                  
                    
          
    $('#year').change(function(){
        const checkboxes = document.querySelectorAll('.yearCheckbox');
        if($('#year').val()!="All")
        {
            $(".cbhide").show();   
            checkboxes.forEach(checkbox => {
                if(checkbox.value==$('#year').val())
                {
                    checkbox.checked=true;   
                    const classDiv = $('#Tahun'+$('#year').val());
                    classDiv.find('input[type=checkbox]').prop('checked', true);
                    classDiv.find('.cb_class').show();
                    
                                            
                }
                    
                else{
                    checkbox.checked=false;
                    const classDiv = $('#Tahun'+checkbox.value);
                    classDiv.find('input[type=checkbox]').prop('checked', false);
                    classDiv.find('.cb_class').hide();
                }
                    

                checkbox.addEventListener('click', () => {      
                    checkYearCheckBox()
                });
            });
        }
        else{
            $(".cbhide").hide();
            checkboxes.forEach((checkbox) => {
            checkbox.checked = false;
            
            });
            const classDivComponent=$('.cb_class');
            const classCheckBox = classDivComponent.find('input[type=checkbox]');
            classCheckBox.prop('checked', false);
            classDivComponent.hide();
            
        }
    });

    
});

function checkYearCheckBox(){
    const checkboxes = document.querySelectorAll('.yearCheckbox');
    const selectedCheckBox=document.querySelectorAll('.yearCheckbox:checked');
                    if(selectedCheckBox.length==checkboxes.length ||selectedCheckBox.length==0)
                    {
                        $(".cbhide").hide();
                        $("#year").val("All");
                        const classDivComponent=$('.cb_class');
                        const classCheckBox = classDivComponent.find('input[type=checkbox]');
                        classCheckBox.prop('checked', false);
                        classDivComponent.hide();
                    }
}

</script>
@endsection