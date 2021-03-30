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
                for (var i=0; i < data.data.length; i++) {
                    var date = new Array();
                    date = moment(data.data[i].latest); 
                    date[i] = date.format('D-M-Y');

                    res +=
                        '<tr>' +
                            '<td>' +
                                '<div>' +
                                    '<img src="assets/images/users/user-2.jpg" alt="" class="avatar-xs rounded-circle mr-2">' +  data.data[i].username +
                                '</div>' +
                            '</td>' +
                            '<td>' + date[i] + '</td>' +
                            '<td> RM ' + data.data[i].amount + '</td>' +
                        '</tr>';
                }

                $('tbody').html(res);
            }
        });

        $.ajax({
            type: 'GET',
            url: '{{ route('dashboard.get_transaction') }}',
            data: {
                id: organizationid,
            },
            success: function(data) {
                var date = new Array();
                var username = new Array();
                var amount = new Array();
                var series = new Array();

                for (var i=0; i < data.data.length; i++) {
                    var NowMoment = moment(data.data[i].datetime_created); 
                    date[i] = NowMoment.format('D-M hh:mm A');
                    username[i] = data.data[i].username;
                    amount[i] = data.data[i].amount;  
                    series = [username,amount];                  
                }

                console.log(series);

                var chart = new Chartist.Line('.ct-chart', {
                    labels: date,
                    series: series
                }, {
                    // Remove this configuration to see that chart rendered with cardinal spline interpolation
                    // Sometimes, on large jumps in data values, it's better to use simple smoothing.
                    // lineSmooth: Chartist.Interpolation.simple({
                    //     divisor: 2
                    // }),
                    // fullWidth: true,
                    // chartPadding: {
                    //     right: 20
                    // },
                    low: 0,
                    plugins: [
                        Chartist.plugins.tooltip()
                    ]
                });

                
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

   

    

   

</script>