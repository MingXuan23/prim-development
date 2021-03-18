@extends('layouts.master')

@section('css')

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
                            <h1 id="donor_day"></h1>
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
                            <h1 id="donor_week"></h1>
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
                            <h1 id="donor_month"></h1>
                            <p class="font-size-14 pt-1">Orang</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="card bg-success">
                    <div class="card-body">
                        <div class="text-center text-white py-4">
                            <h5 class="mt-0 mb-4 text-white-50 font-size-16">Derma Terkumpul Hari Ini (RM)</h5>
                            <h1 id="donation_day"></h1>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success">
                    <div class="card-body">
                        <div class="text-center text-white py-4">
                            <h5 class="mt-0 mb-4 text-white-50 font-size-16">Derma Terkumpul Minggu Ini (RM)</h5>
                            <h1 id="donation_week"></h1>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success">
                    <div class="card-body">
                        <div class="text-center text-white py-4">
                            <h5 class="mt-0 mb-4 text-white-50 font-size-16">Derma Terkumpul Bulan Ini (RM)</h5>
                            <h1 id="donation_month"></h1>
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

        {{-- <script src="{{ URL::asset('assets/js/pages/dashboard.init.js')}}"></script> --}}

        <script>
            
            // on change event for organization_dropdown
             $('#organization_dropdown').change(function() {
                var organizationid = $("#organization_dropdown option:selected").val();
                
                $.ajax({
						type: 'GET',
						url: '{{ route("dashboard.item") }}',
						data: {
                            id : organizationid
                        },
						success: function(data){

                            var donation = JSON.parse(data);
							var donor_day      = donation.donor_day[0].donor;
							var donor_week     = donation.donor_week[0].donor;
							var donor_month    = donation.donor_month[0].donor;
                            var donation_day   = donation.donation_day[0].donation_amount;
							var donation_week  = donation.donation_week[0].donation_amount;
							var donation_month = donation.donation_month[0].donation_amount;

                            document.getElementById("donor_day").innerHTML = donor_day;
                            document.getElementById("donor_week").innerHTML = donor_week;
                            document.getElementById("donor_month").innerHTML = donor_month;
                            document.getElementById("donation_day").innerHTML = donation_day;
                            document.getElementById("donation_week").innerHTML = donation_week;
                            document.getElementById("donation_month").innerHTML = donation_month;
						}
					});
            });
        </script>
@endsection