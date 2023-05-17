@extends('layouts.master')
@section('css')
<style>
   @media (max-width: 767px) {
  #buttonGroup {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: center;
  }
  
  #buttonGroup h4 {
    flex-basis: 100%;
    margin-bottom: 10px;
  }

  #buttonGroup span {
    flex-basis: 100%;
    margin-bottom: 10px;

  }
  
  #buttonGroup a {
    flex-basis: 100%;
    width: 100%
    
  }
}


</style>

<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ URL::asset('assets/css/datatable.css')}}">
@include('layouts.datatable')

@endsection


@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="page-title-box">
      <h4 class="font-size-18">{{ $koperasi->nama }}</h4>
      <!-- <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Welcome to Veltrix Dashboard</li>
            </ol> -->
    </div>
  <div class="col-md-12">
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
@if(\Session::has('success'))
<div class="alert alert-success">
  <p>{{ \Session::get('success') }}</p>
</div>
@endif



<div class="flash-message"></div>
    <div class="card">
    <div class="card-body">
      {{-- <div class="card-header">List Of Applications</div> --}}
     
      <div id="buttonGroup">
      <h4>Manage Product and Product Type</h4>
      <span style="margin-right: 15px;">
        <a  href="{{route('koperasi.addtype')}}" class="btn btn-primary"> <i
            class="fas fa-plus"></i> Urus jenis produk</a></span>
            
        
     
            @if(count($group)>0)
            <span style="margin-right: 15px;"> 
                <a  href="{{route('koperasi.createProduct')}}" class="btn btn-primary"> <i
                    class="fas fa-plus" ></i> Tambah produk</a></span>
                    <span style="margin-right: 15px;"> 
                    <a  href="#" class="btn btn-success" data-toggle="modal" data-target="#modelId" style="float:right;" > <i
                    class="fas fa-plus" ></i> Import produk</a></span>
              
            @else
            <span class="font-size-18"> 
               Anda perlu tambah jenis produk dahulu</span>
            @endif

      </div>
<br><br>
{{-- confirmation delete modal --}}
            <div id="deleteConfirmationModal" class="modal fade" role="dialog">
              <div class="modal-dialog">
                <div class="modal-content">
                  <div class="modal-header">
                    <h4 class="modal-title">Padam Product</h4>
                  </div>
                  <div class="modal-body">
                    Adakah anda pasti?
                  </div>
                  <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-primary" id="delete" name="delete" onclick="selectedItems()">Padam</button>
                    <button type="button" data-dismiss="modal" class="btn">Batal</button>
                  </div>
                </div>
              </div>
            </div>
            {{-- end confirmation delete modal --}}

<div class="table-responsive">
    
    <table id="productTable" class="table table-bordered table-striped dt-responsive"
            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
            <thead>
              <tr style="text-align:center">
              <th></th>  
              <th> No. </th>
                <th> Nama Produk </th>
                <th> Penerangan </th>
                <th> Gambar </th>
                <th> Kuantiti </th>
                <th> Harga </th>
                <th> Status </th>
                <th>Type</th>
                <th> Action </th>
              </tr>
            </thead>
          </table>     
          
          <div class="mt-3">
          <span style="margin-right: 15px;">
          <button class="btn btn-primary" id="select-btn" onclick="selectAll()">Select All</button></span>
      <span > 
        
      <button class="btn btn-danger" id="delete-btn" style="display:none" >Delete Selected</button></span>
      </div>

        
</div>
{{--import modal--}}
<div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Import Product</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('importproduct') }}" method="post" enctype="multipart/form-data">
                        <div class="modal-body">

                            {{ csrf_field() }}
                            <label>* Jenis Produk</label></br>

                            <div class="col">
                                                <div class="form-group required">
                                                    <select name="type" id="type" class="form-control"
                                                        data-parsley-required-message="Sila masukkan jenis produk" required>
                                                        @foreach($group as $row)
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
                            <input type="hidden" name="classCheckBoxEmpty" id="classCheckBoxEmpty" value="false"> 
                            <br>
                            <div class="form-group">
                                <input type="file" name="file" required>
                            </div>
                            
                            <input type="hidden" name="organ" id="organ" value="{{$koperasi->organization_id}}">
                        
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary" onclick="checkByClassOrByYear()">Import</button>
                                <!-- to check is any class checkbox is unchecked -->

                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>       
{{--end import modal--}}
@section('script')


<script>

function loadProducts() {
  
  //console.log("TABLE run");
  var productTable = $('#productTable').DataTable({
                  processing: true,
                  serverSide: true,
                  ajax: {
                      url: "{{ route('koperasi.getProductList') }}",
                      type: 'GET',
                  },
                  'columnDefs': [{
                    
                      "targets": [0], // your case first column
                      "className": "text-center",
                      "width": "2%"
                  },{
                      "targets": [3,4,5,6], // your case first column
                      "className": "text-center",
                  },],
                  order: [
                      [2, 'asc']
                  ],
                  columns: [{
                    "data":null,
                     "sortable":false,
                     searchable:false,
                     "className": "text-center",
                     render: function (data, type, row, meta) {
                      
                      return '<input type="checkbox" class="product-checkbox" value ="' + data.id + '">';    
                    }          
                  }
                  ,{
                      "data": null,
                      searchable: false,
                      "sortable": false,
                      render: function (data, type, row, meta) {
                          return meta.row + meta.settings._iDisplayStart + 1;
                      }
                  }, {
                      data: "name",
                      name: 'name',      
                      "width": "30%"
                  }, {
                      data: "desctext",
                      name: 'description',
                      "width": "30%",
                      searchable: true
                  },{
                      data: "image",
                      name: "image",
                      searchable: false,
                      sortable: false,
                      render: function(data, type, row, meta) {
                          return '<img src="/koperasi-item/' + row.image + '" style="max-width:100px;">';
    }
                  }, {
                      data: "quantity_available",
                      name: 'quantity',
                      "className": "text-center",
                      "width": "10%"
                  }, 
                  {
                      data: "price",
                      name: 'Price',
                      "className": "text-center",
                      "width": "10%"
                  },{
                      data: 'status',
                      name: 'status',
                      orderable: false,
                      searchable: false,
                      "className": "text-center",
                  }, {
                      data: "type_name",
                      name: 'Type',
                      "className": "text-center",
                      "width": "10%",
                      searchable: true
                  }, {
                      data: 'action',
                      name: 'action',
                      orderable: false,
                      searchable: false,
                      "className": "text-center",
                      "width": "20%"
                  }]
                  
                  ,drawCallback: function() {
                    // Call your second function here
                    initialise();
                  }
          });
          //console.log("table end");
         
}


//to use ajax
$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });



function initialise()
{
  //console.log("checkbox run");
//if no data inside table
const checkboxes = document.querySelectorAll('input[type="checkbox"]');
const deleteBtn = document.getElementById('delete-btn');
const selectBtn =document.getElementById('select-btn');
if(checkboxes.length==0)
{
  //console.log(checkboxes.length);
    selectBtn.style.display='none';
}

//add click listener to each checkbox
checkboxes.forEach(checkbox => {
  checkbox.addEventListener('click', () => {
    //if all checkbox is selected
    if(checkboxes.length==document.querySelectorAll('.product-checkbox:checked').length) {
      selectBtn.innerHTML = "Deselect All";
      deleteBtn.style.display = 'inline';
    }//at least one checkbox is selected
    else if (document.querySelectorAll('.product-checkbox:checked').length > 0) {
      deleteBtn.style.display = 'inline';
    }//no checkbox is selected
    else {
      deleteBtn.style.display = 'none';
      selectBtn.innerHTML = "Select All";
    }
  });
});
//console.log("checkbox end");



}

$(document).ready(function() {

 loadProducts();
 //initialise(checkboxes);
  
  $(document).on('click', '.btn-danger', function(){
          $('#deleteConfirmationModal').modal('show');
      });
 
$('.alert').delay(3000).fadeOut();
initialiseTargetCheckBox();
//to use modal import 


});


//selectall
function selectAll()
{
  const checkboxes = document.querySelectorAll('input[type="checkbox"]');
  const deleteBtn = document.getElementById('delete-btn');
const selectBtn =document.getElementById('select-btn');
  if (selectBtn.innerHTML === "Deselect All") {
    checkboxes.forEach((checkbox) => {
      checkbox.checked = false;
    });
    selectBtn.innerHTML = "Select All";
    deleteBtn.style.display = 'none';
  } else {
    checkboxes.forEach((checkbox) => {
      checkbox.checked = true;
      
    });
    selectBtn.innerHTML = "Deselect All";
    deleteBtn.style.display = 'inline';
  
  }
  
 
}

function selectedItems() {

let itemArray = [];
const refreshTable =document.getElementById('productTable');
const selectedCheckBox = document.querySelectorAll('.product-checkbox:checked');
const deleteBtn = document.getElementById('delete-btn');
const selectBtn =document.getElementById('select-btn');
const checkboxes = document.querySelectorAll('input[type="checkbox"]');
var table=$('#productTable').DataTable();
selectedCheckBox.forEach((checkbox) => {
  itemArray.push(checkbox.value);
});
//all selected checkbox is here
$.ajax({
    url: '{{route("koperasi.deleteSelectedProducts")}}',
    type: 'POST',
    data: { itemArray: itemArray },
    success: function(response) {
      console.log('Success:', response);
      $('#delete-btn').hide();
      table.ajax.reload(); //refresh the page to reload the data
    },
    error: function(xhr, status, error) {
      console.log(error);
    }
  });
}

function initialiseTargetCheckBox(){
    $.ajax({
        url: "{{ route('koperasi.fetchClassYear') }}",
        method: "GET",
        success: function(result) {
                     jQuery.each(result.datayear, function(key, value) {
                         $("#year").append("<option value='"+ value.year +"'> Tahun " + value.year + "</option>");
                         let htmlContent="<div id='Tahun"+value.year+"' class='form-check-inline pb-3 pt-3'>";

                         htmlContent+="<label for='form-check-label' style='margin-right: 22px;' class='form-check-label'>";
                         htmlContent+="<input class='yearCheckbox' data-parsley-required-message='Sila pilih tahun' data-parsley-errors-container='.errorMessageCB' type='checkbox' id='cb_year' name='cb_year[]' value='T" +
                             value.year + "'/> " +"Tahun " +value.year + "</label>";
                         const filteredClass = result.classes.filter(c => c.nama.startsWith(value.year));
                         //console.log(filteredClass);
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
                         const classDiv = $('#Tahun'+$(this).val().charAt(1));
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
                         // console.log("Select:"+selectedCheckBox.length);
                         // console.log("All :"+classCheckBox.length)
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
         if(checkbox.value.charAt(1)==$('#year').val())
         {
             checkbox.checked=true;   
             const classDiv = $('#Tahun'+$('#year').val());
             classDiv.find('input[type=checkbox]').prop('checked', true);
             classDiv.find('.cb_class').show();
             
                                     
         }
             
         else{
             checkbox.checked=false;
             const classDiv = $('#Tahun'+checkbox.value.charAt(1));
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
}

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

function checkByClassOrByYear(){
    console.log("run")
    const divCheckBox = document.querySelectorAll('.form-check-inline');
    console.log(divCheckBox.length);
    divCheckBox.forEach(singleDiv=>{
        let classCheckBox = singleDiv.querySelectorAll('input[type=checkbox]');
        let selectedCheckBox=Array.from(classCheckBox).filter(checkbox => checkbox.checked);
        if(selectedCheckBox.length==0){
            //do nothing
            console.log("no selected");
        }
        else if(selectedCheckBox.length!=classCheckBox.length){
            $('#classCheckBoxEmpty').val(true);
           
            //console.log();
            console.log($('#classCheckBoxEmpty').val());
            
        }
    });
}

</script>
@endSection

@endsection
