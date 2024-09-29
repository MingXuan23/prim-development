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
            <h2>S Helper Reminder</h2>
        </div>
        <div class="content">
        <p>You currently have {{ $pending_request }} requests awaiting your assistance. Your help is needed now.</p>
            
        <div class="button-container">
            <a href="{{ route('codereq.list_by_helper') }}" class="redirect-button">Help Them Now</a>
        </div>
            
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} All rights reserved | PRiM</p>
        </div>
    </div>
</body>
</html>