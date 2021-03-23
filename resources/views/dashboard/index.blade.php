<script>
    // on change event for organization_dropdown
    var organizationid
    $('#organization_dropdown').change(function() {
        organizationid = $("#organization_dropdown option:selected").val();
        
    });

    function getTotalDonation(id){
            var duration;
            var totalDonationDay; 

            if (id == "btn_day") {
                duration = "day";
                document.getElementById("p_donor_day").innerHTML = "Hari Ini"
            } else if (id == "btn_week") {
                duration = "week";
                document.getElementById("p_donor_day").innerHTML = "Minggu Ini"
            } else if (id == "btn_month") {
                duration = "month";
                document.getElementById("p_donor_day").innerHTML = "Bulan Ini"
            }
            
            $.ajax({
                type: 'GET',
                url: '{{ route('dashboard.totalDonation') }}',
                data: {
                    id: organizationid,
                    duration: duration
                },
                success: function(data) {
                    duration = data.data.duration;
                    console.log(duration);
                    if (duration == "day") {
                        document.getElementById("total_donation").innerHTML = (data.data.donation_amount === null) ? "RM 0.00" : "RM " + data.data.donation_amount;
                    } else if (duration == "week") {
                        document.getElementById("total_donation").innerHTML = (data.data.donation_amount === null) ? "RM 0.00" : "RM " + data.data.donation_amount;
                    } else if (duration == "month") {
                        document.getElementById("total_donation").innerHTML = (data.data.donation_amount === null) ? "RM 0.00" : "RM " + data.data.donation_amount;
                    }
                }
            });
        }

    var chart = new Chartist.Line('.ct-chart', {
        labels: ['01/02 10.00 pm', '06/03 3.00 pm', '09/04 11.00 am', '10/04 6.00 am', ''],
        series: [
            [{
                meta: 'Robert Sitton',
                value: 15
            }, {
                meta: 'Brent Shipley',
                value: 4
            }, {
                meta: 'Philip Smead',
                value: 90
            }, {
                meta: 'Adi Iman',
                value: 40
            }, 0]
        ]
    }, {
        // Remove this configuration to see that chart rendered with cardinal spline interpolation
        // Sometimes, on large jumps in data values, it's better to use simple smoothing.
        lineSmooth: Chartist.Interpolation.simple({
            divisor: 2
        }),
        fullWidth: true,
        chartPadding: {
            right: 20
        },
        low: 0,
        plugins: [
            Chartist.plugins.tooltip()
        ]
    });

   

</script>