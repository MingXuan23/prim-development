@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
@include('layouts.datatable')

@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">{{ $category->nama }}</h4>
        </div>
    </div>
</div>
<div class="row">

    <div class="col-md-12">
        <div class="card">
            <div>
                {{-- route('sekolah.create')  --}}
                <a style="margin: 19px; float: right;" href="{{ route('details.create', ['id' => request()->id] ) }}" class="btn btn-primary"> <i
                    class="fas fa-plus"></i> Tambah Butiran</a>
              </div>
            <div class="card-body">



                <div class="table-responsive">
                    <table id="DetailsTable" class="table table-bordered table-striped dt-responsive nowrap"
                        style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                        <thead>
                            <tr style="text-align:center">
                                <th> No. </th>
                                <th>Nama Kategori</th>
                                <th>Kuantiti</th>
                                <th>Harga (RM)</th>
                                <th>Jumlah Amaun (RM)</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        {{-- confirmation delete modal --}}
        <div id="deleteConfirmationModal" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Padam Kategori</h4>
                    </div>
                    <div class="modal-body">
                        Adakah anda pasti?
                    </div>
                    <div class="modal-footer">
                        <button type="button" data-dismiss="modal" class="btn btn-primary" id="delete"
                            name="delete">Padam</button>
                        <button type="button" data-dismiss="modal" class="btn">Batal</button>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
{{-- {{ route('category.getCategoryDetailsDatatable') }} --}}

@section('script')
<!-- Peity chart-->
<script src="{{ URL::asset('assets/libs/peity/peity.min.js')}}"></script>

<!-- Plugin Js-->
<script src="{{ URL::asset('assets/libs/chartist/chartist.min.js')}}"></script>

<script src="{{ URL::asset('assets/js/pages/dashboard.init.js')}}"></script>

<script>
    $(document).ready(function() {
  
      var DetailsTable;
  
        fetch_data();
  
        function fetch_data() {
            DetailsTable = $('#DetailsTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('category.getCategoryDetailsDatatable') }}",
                        data: {
                            catid: "{{ request()->id }}",
                        },
                        type: 'GET',
  
                    },
                    'columnDefs': [{
                        "targets": [0], // your case first column
                        "className": "text-center",
                        "width": "2%"
                    },{
                        "targets": [2,3,4], // your case first column
                        "className": "text-center",
                    },],
                    order: [
                        [1, 'asc']
                    ],
                    columns: [{
                        "data": null,
                        searchable: false,
                        "sortable": false,
                        render: function (data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    }, {
                        data: "nama",
                        name: 'nama',
                    }, {
                        data: "quantity",
                        name: 'quantity',
                        width: "10%",
                        orderable: false,
                        searchable: false
                    }, {
                        data: 'price',
                        name: 'price',
                        width: "10%",
                        orderable: false,
                        searchable: false,
                        defaultContent: 0,
                        render: function(data, type, full) {
                            if(data){
                                return parseFloat(data).toFixed(2);
                            }else{
                                return 0;
                            }
                        }
                    },{
                        data: 'totalamount',
                        name: 'totalamount',
                        width: "10%",
                        orderable: false,
                        searchable: false,
                        defaultContent: 0,
                        render: function(data, type, full) {
                            if(data){
                                return parseFloat(data).toFixed(2);
                            }else{
                                return 0;
                            }
                        }
                    }, {
                        data: 'action',
                        name: 'action',
                        width: "20%",
                        orderable: false,
                        searchable: false
                    },]
            });
        }

        // csrf token for ajax
        $.ajaxSetup({
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
  
        var teacher_id;
  
        $(document).on('click', '.btn-danger', function(){
            teacher_id = $(this).attr('id');
            $('#deleteConfirmationModal').modal('show');
        });
  
        $('#delete').click(function() {
              $.ajax({
                  type: 'POST',
                  dataType: 'html',
                  data: {
                      "_token": "{{ csrf_token() }}",
                      _method: 'DELETE'
                  },
                  url: "/teacher/" + teacher_id,
                  success: function(data) {
                      setTimeout(function() {
                          $('#confirmModal').modal('hide');
                      }, 2000);
  
                      $('div.flash-message').html(data);
  
                      categoryTable.ajax.reload();
                  },
                  error: function (data) {
                      $('div.flash-message').html(data);
                  }
              })
          });
          
          $('.alert').delay(3000).fadeOut();
  
    });
</script>

@endsection