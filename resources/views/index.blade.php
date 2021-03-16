@extends('layouts.master')

@section('css')
        <link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<!-- start page title -->
<div class="row align-items-center">
    <div class="card-body">
        <div class="form-group">
          <select name="organization" id="organization_dropdown" class="form-control">
            <option value="" selected>Pilih Organisasi</option>
            @foreach($organizations as $organization)
                <option value="{{ $organization->id }}">{{ $organization->nama }}</option>
            @endforeach
          </select>
        </div>
        <div class="row">
            <div class="col-xl-4 col-md-4">
                <div class="card bg-primary">
                    <div class="card-body">
                        <div class="text-center text-white py-4">
                            <h5 class="mt-0 mb-4 text-white-50 font-size-16">Jumlah Penderma Hari Ini</h5>
                            @foreach($donorsDays as $donor)
                            <h1>{{ $donor->donor }}</h1>
                            @endforeach
                            <p class="font-size-14 pt-1">Orang</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-primary">
                    <div class="card-body">
                        <div class="text-center text-white py-4">
                            <h5 class="mt-0 mb-4 text-white-50 font-size-16">Jumlah Penderma Minggu Ini</h5>
                            @foreach($donorsWeeks as $donor)
                            <h1>{{ $donor->donor }}</h1>
                            @endforeach
                            <p class="font-size-14 pt-1">Orang</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-primary">
                    <div class="card-body">
                        <div class="text-center text-white py-4">
                            <h5 class="mt-0 mb-4 text-white-50 font-size-16">Jumlah Penderma Bulan Ini</h5>
                            @foreach($donorsMonths as $donor)
                            <h1>{{ $donor->donor }}</h1>
                            @endforeach
                            <p class="font-size-14 pt-1">Orang</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      </div>
</div>
<!-- end page title -->

                       
@endsection

@section('script')
        <!-- Peity chart-->
        <script src="{{ URL::asset('assets/libs/peity/peity.min.js')}}"></script>

        <!-- Plugin Js-->
        <script src="{{ URL::asset('assets/libs/chartist/chartist.min.js')}}"></script>

        <script src="{{ URL::asset('assets/js/pages/dashboard.init.js')}}"></script>

        <script>
            
            // on change event for organization_dropdown
             $('#organization_dropdown').change(function() {
                var organizationid = $("#organization_dropdown option:selected").val();
                // $('#donationTable').DataTable().destroy();
                console.log(organizationid);
                
                $.ajax({
						type: 'GET',
						url: '{{ route("donor") }}',
						data: {
                            id : organizationid
                        },
						success: function(data){

                            var donation = JSON.parse(data);
							var day      = donation.day[0].donor);
							var week     = donation.week[0].donor);
							var month    = donation.month[0].donor);

                            
						}
					});
            });
        </script>
@endsection