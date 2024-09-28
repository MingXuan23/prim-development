<!-- resources/views/emails/helper_receipt.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>S Helper Receipt</title>
    <style>


        body { font-family: Arial, sans-serif; line-height: 1.2; color: #333; background-color: #f0f8ff; }
    .container { width: 100%; max-width: 600px; margin: 0 auto; padding: 20px; background-color: white; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    .header { background-color: #0066cc; padding: 10px; text-align: center; color: white; }
    .content { padding: 20px; }
    .footer { background-color: #0066cc; padding: 10px; text-align: center; font-size: 0.8em; color: white; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
    th, td { border: 1px solid #bbd6ff; padding: 8px; text-align: left; }
    th { background-color: #e6f2ff; color: #0066cc; }
    .redirect-button {
            display: inline-block;
            margin: 0 auto;
            padding: 10px 20px;
            background-color: #0066cc;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
            width:90%
        }
        .button-container {
            text-align: center; /* Center align the button */
            margin-top: 20px;
            
        }
        .redirect-button:hover {
            background-color: #005bb5;
        }
    
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>S Helper Receipt</h2>
        </div>
        <div class="content">
            <p>{{ $codeRequest->name }},</p>
            
            <table>
                <tr>
                    <th>Reference Num</th>
                    <td>{{ $codeRequest->id }}-{{$codeRequest->transaction_id}}</td>
                </tr>
                <tr>
                    <th>Transaction No</th>
                    <td> {{$details->transac_no }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $codeRequest->email }}</td>
                </tr>
                <tr>
                    <th>Phone</th>
                    <td>{{ $codeRequest->phone }}</td>
                </tr>
                <tr>
                    <th>Language</th>
                    <td>{{ $details->language_name }}</td>
                </tr>
                <tr>
                    <th>Package</th>
                    <td>{{ $details->package_name }}</td>
                </tr>
                <tr>
                    <th>Amount</th>
                    <td>RM {{ number_format($codeRequest->final_price, 2) }}</td>
                </tr>
                

                
            </table>
            <div class="button-container">
            <a href="{{ route('codereq.showlist') }}" class="redirect-button">Check Helper Status</a>
    </div>
            <h4>With Problem Description:</h4>
            <p>{{ $codeRequest->problem_description }}</p>

           <hr>
            <p>Our helpers will try to contact and help you as soon as possible.</p>
            <p>Thank you for your patient!</p>
           
            
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} All rights reserved | PRiM</p>
        </div>
    </div>
</body>
</html>