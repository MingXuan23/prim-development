<!-- resources/views/send-request.blade.php -->
@extends('layouts.master')

@section('css')
       <style>
            .navbar-header .btn {
                display: none;
            }

            input[type=radio] {
                display:none
            }

            .more-info {
  background-color: #fff;
  border-radius: 15px;

  box-shadow: 0 10px 30px rgba(0,0,0,0.1);
 width:100%;
  margin: 30px auto;
}
.more-info-header {
  cursor: pointer;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px 20px;
  background: linear-gradient(135deg, #6e8efb, #a777e3);
  border-radius: 10px;
  color: white;
  transition: all 0.3s ease;
}
.more-info-header:hover {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
.more-info-content {
  padding: 20px 20px;
  display: none;
}

.process-flow {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 30px;
  flex-wrap: wrap;
}
.step-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 22%; /* Adjust based on your preference */
  text-align: center;
}
.step {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: linear-gradient(135deg, #6e8efb, #a777e3);
  color: white;
  display: flex;
  justify-content: center;
  align-items: center;
  font-weight: bold;
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  margin-bottom: 10px;
}
.step-text {
  font-size: 14px;
  line-height: 1.4;
  color: #333;
}
.arrow {
  flex-grow: 1;
  height: 2px;
  background-color: #a777e3;
  position: relative;
  margin: 30px 10px 0;
  align-self: flex-start;
}
.arrow::after {
  content: '';
  position: absolute;
  right: -6px;
  top: -4px;
  width: 0;
  height: 0;
  border-left: 10px solid #a777e3;
  border-top: 5px solid transparent;
  border-bottom: 5px solid transparent;
}

@media (max-width: 768px) {
  .process-flow {
    flex-direction: column;
    align-items: center;
  }
  .step-container {
    width: 100%;
    margin-bottom: 20px;
  }
  .arrow {
    width: 2px;
    height: 20px;
    margin: 10px auto;
  }
  .arrow::after {
    right: -4px;
    top: auto;
    bottom: -6px;
    border-left: 5px solid transparent;
    border-right: 5px solid transparent;
    border-top: 10px solid #a777e3;
    border-bottom: none;
  }
}

table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
th, td {
  padding: 12px 15px;
  text-align: left;
  border-bottom: 1px solid #eee;
}
th {
  background-color: #6e8efb;
  color: white;
  font-weight: 600;
}
tr:last-child td {
  border-bottom: none;
}
.price {
  font-weight: 600;
  color: #6e8efb;
}

       </style>
@endsection


@section('content')
<div class="container">
    <h2>S Helper</h2>
   
    @if(\Session::has('success'))
    <div class="alert alert-success">
        <p id="success">{{ \Session::get('success') }}</p>
    </div>
    @endif
    @if(\Session::has('error'))
    <div class="alert alert-danger">
        <p id="success">{{ \Session::get('error') }}</p>
    </div>
    @endif

    <form id="requestForm" method="POST" action="{{ route('codereq.store') }}">
        @csrf

        <div class="row">
            <!-- Name -->
            <div class="col-lg-6">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" class="form-control" id="name" name="name" required placeholder="Your name">
                </div>
            </div>

            <!-- Email -->
            <div class="col-lg-6">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" class="form-control" id="email" name="email" required placeholder="Your email"
                    >
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Phone Number -->
            <div class="col-lg-6">
                <div class="form-group">
                    <label for="phone">Phone Number:</label>
                    <input type="text" name="telno" id="telno"
                                            class="form-control phone_no"
                                            placeholder="Your phone number" max="11">
                </div>
            </div>

            <!-- Language -->
            <div class="col-lg-6">
                <div class="form-group">
                    <label for="language">Language:</label>
                    <select class="form-control" id="language" name="language" required>
                        @foreach($languages as $language)
                            <option value="{{ $language->id }}">{{ $language->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Other fields -->
        <div class="form-group">
    <div class="row">
        <label for="type" class="col-12 col-md-2 mb-2 mb-md-0">Type:</label>
        <div class="col-12 col-md-5 mb-2 mb-md-0">
            <div class="form-check h-100">
                <input class="form-check-input" type="radio" name="type" id="typeA" value="A" required checked>
                <label class="form-check-label form-control h-100 d-flex align-items-center justify-content-center" for="typeA">
                    <span>A: Simple Task (Debugging)</span>
                </label>
            </div>
        </div>
        <div class="col-12 col-md-5">
            <div class="form-check h-100">
                <input class="form-check-input" type="radio" name="type" id="typeB" value="B" required>
                <label class="form-check-label form-control h-100 d-flex align-items-center justify-content-center" for="typeB">
                    <span>B: Complex Task (Project Assistance)</span>
                </label>
            </div>
        </div>
    </div>
</div>

<div class ="row packageDiv" >
        <div class="col-lg-12">
                        <div class="form-group">
                            <label for="package">Package:</label>
                            <select class="form-control" id="package" name="package" required>
                            @foreach($packages->where('type', 'B') as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                            </select>
                        </div>
                    </div>
</div>


        <div class="form-group">
            <label for="source_code">Source Code:</label>
            <textarea class="form-control" id="source_code" name="source_code" rows="5" required 
            placeholder = "Enter your code here. All comments will be automatically removed before sending it for assistance."></textarea>
        </div>
        <div class="form-group">
            <label for="problem_description">Problem Description:</label>
            <textarea class="form-control" id="problem_description" name="problem_description" rows="3" required
            placeholder="Describe the error you're encountering or the problem you're trying to solve. "></textarea>
        </div>
        
        <input type="hidden" name="session_id" id="session_id" value="">
        <input type="hidden" name="price" id="price" value="">

        <button type="button" class="btn btn-primary " id="calculatePriceButton" >Calculate Price</button>

        <button type="submit" class="btn btn-primary ml-3" id="payButton" >Request & Pay</button>
    </form>
    <div class="more-info">
  <div class="more-info-header" id="moreInfoToggle">
    <h2>More Info</h2>
    <span id="toggleIcon">▼</span>
  </div>
  <div class="more-info-content" id="moreInfoContent">
    <h3>S Helper Process</h3>
    <div class="process-flow">
      <div class="step-container">
        <div class="step">1</div>
        <div class="step-text">Ask question about your code</div>
      </div>
      <div class="arrow"></div>
      <div class="step-container">
        <div class="step">2</div>
        <div class="step-text">Pay based on package</div>
      </div>
      <div class="arrow"></div>
      <div class="step-container">
        <div class="step">3</div>
        <div class="step-text">Our helpers will respond ASAP within 24 hours</div>
      </div>
      <div class="arrow"></div>
      <div class="step-container">
        <div class="step">4</div>
        <div class="step-text">The estimated time to complete is within 72 hours</div>
      </div>
    </div>
    
    <h3>A: Simple Task (Debugging)</h3>
    <table>
      <thead>
        <tr>
          <th>Package</th>
          <th>Price</th>
        
        </tr>
      </thead>
      <tbody>
      @foreach($packages->where('type', 'A') as $p)
        <tr>
            <td>{{$p->name}}</td>
            <td class="price">RM {{$p->price}}</td>
        
        </tr>
        @endforeach
       
      
      </tbody>
    </table>
<br>
    <h3>B: Complex Task (Project Assistance)</h3>
    <table>
      <thead>
        <tr>
          <th>Package</th>
          <th>Price</th>
        
        </tr>
      </thead>
      <tbody>
      @foreach($packages->where('type', 'B') as $p)
        <tr>
            <td>{{$p->name}}</td>
            <td class="price">RM {{$p->price}}</td>
        
        </tr>
        @endforeach
       
       
      
      </tbody>
    </table>
  </div>
</div>
</div>


@endsection


@section('script')
<script src="{{ URL::asset('assets/libs/jquery-mask/jquery.mask.min.js')}}"></script>
<script src="{{ URL::asset('assets/libs/peity/peity.min.js')}}"></script>
<script>
    $(document).ready(function() {
        $('.phone_no').mask('+600000000000');


        $('#calculatePriceButton').click(function() {
            $('#calculatePriceButton').prop('disabled', true);
            var formData = $('#requestForm').serialize(); // Serialize the form data for submission

            // Perform the AJAX request
            $.ajax({
                url: "{{ route('codereq.validateRequest') }}", // URL for the route
                type: 'POST',
                data: formData,
                success: function(response) {
                   if(response.status == "success"){
                        $('.line-count').remove();
                        var successDiv = $('<div class="badge badge-success line-count ml-3"><span class="h6 mb-0">' + response.num_of_statement + ' lines statement!</span></div>');

                        // Append the success div next to the label
                        $('.form-group label[for="source_code"]').after(successDiv);
                        $('#payButton').text('Request & Pay RM ' + response.price.toFixed(2) );
                        $('#session_id').val(response.session_id);
                        $('#price').val(response.price.toFixed(2));

                        $('#payButton').prop('disabled', false);


                    }else if(response.status == "error"){
                            alert(response.message);
                    }

            $('#calculatePriceButton').prop('disabled', false);

                },
                error: function(xhr) {
                    // Handle errors
                    alert('An error occurred while calculating the price.');
            $('#calculatePriceButton').prop('disabled', false);

                }
            });


        });

       

        // Attach change event listeners
        $('input:not([type="hidden"]), #problem_description, #source_code').on('change keyup', function() {
            //console.log('tets');
            $('#payButton').text('Request & Pay');

            if($('#session_id').val()){
                $('#session_id').val($('#session_id').val() + "-0"); 
            }
            $('#payButton').prop('disabled', true);
        });

        $('input[type=radio][name=type]').change(function() {
            // Remove success class from all labels

            $('.form-check-label').removeClass('text-white bg-success');
            
            // Add success class to the selected radio button's label
            $('label[for=' + this.id + ']').addClass('text-white bg-success');

            if ($(this).val() === 'B') {
                $('.packageDiv').show();  // Show dropdown
            } else {
                $('.packageDiv').hide();  // Hide dropdown
            }
        });

        $('input[type=radio][name=type]:checked').trigger('change');

        $('#moreInfoToggle').click(function() {
    const moreInfoContent = $('#moreInfoContent');
    const toggleIcon = $('#toggleIcon');
    
    moreInfoContent.slideToggle(300, function() {
      toggleIcon.text(moreInfoContent.is(':visible') ? '▲' : '▼');
    });
  });

  $('.step').hover(
    function() {
      $(this).css({
        'transform': 'scale(1.1) rotate(5deg)',
        'box-shadow': '0 10px 20px rgba(0,0,0,0.2)'
      });
    }, 
    function() {
      $(this).css({
        'transform': 'scale(1) rotate(0deg)',
        'box-shadow': '0 5px 15px rgba(0,0,0,0.1)'
      });
    }
  );


        $('.alert').delay(3000).fadeOut();
       
    });
</script>
@endsection