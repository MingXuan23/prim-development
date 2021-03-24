<script>
    // on change event for organization_dropdown
    var organizationid
    $('#organization_dropdown').change(function() {
        organizationid = $("#organization_dropdown option:selected").val();
        
        $.ajax({
            type: 'GET',
            url: '{{ route('dashboard.latest_transaction') }}',
            data: {
                id: organizationid,
            },
            success: function(data) {
            var res='';
                $.each(data, function(key, value) {
                    console.log(data);
                    
                    res +=
                        '<tr>' +
                            '<td>' +
                                '<div>' +
                                    '<img src="assets/images/users/user-2.jpg" alt="" class="avatar-xs rounded-circle mr-2">' +  data.data[0].username +
                                '</div>' +
                            '</td>' +
                            '<td>' + data.data[0].latest + '</td>' +
                            '<td>' + data.data[0].amount + '</td>' +
                        '</tr>';
                });

                $('tbody').html(res);
            }
        });

    });

    function getTotalDonation(id){
            var duration;
            var totalDonationDay; 

            if (id == "btn_day") {
                duration = "day";
                document.getElementById("p_donation_day").innerHTML = "Hari Ini"
            } else if (id == "btn_week") {
                duration = "week";
                document.getElementById("p_donation_day").innerHTML = "Minggu Ini"
            } else if (id == "btn_month") {
                duration = "month";
                document.getElementById("p_donation_day").innerHTML = "Bulan Ini"
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

    function getTotalDonor(id){
        var duration;
        var totalDonationDay; 
        
        if (id == "btn_donor_day") {
            duration = "day";
            document.getElementById("p_donor_day").innerHTML = "Hari Ini"
        } else if (id == "btn_donor_week") {
            duration = "week";
            document.getElementById("p_donor_day").innerHTML = "Minggu Ini"
        } else if (id == "btn_donor_month") {
            duration = "month";
            document.getElementById("p_donor_day").innerHTML = "Bulan Ini"
        }
        
        $.ajax({
            type: 'GET',
            url: '{{ route('dashboard.totalDonor') }}',
            data: {
                id: organizationid,
                duration: duration
            },
            success: function(data) {
                duration = data.data.duration;
                if (duration == "day") {
                    document.getElementById("total_donor").innerHTML = (data.data.donor === null) ? 0 : data.data.donor;
                } else if (duration == "week") {
                    document.getElementById("total_donor").innerHTML = (data.data.donor === null) ? 0 : data.data.donor;
                } else if (duration == "month") {
                    document.getElementById("total_donor").innerHTML = (data.data.donor === null) ? 0 : data.data.donor;
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