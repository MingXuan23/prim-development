<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <script type="text/javascript">
        // Automatically submit the form when the page loads
        window.onload = function() {
            document.getElementById('bayarform').submit();
        };
    </script>
</head>
<body>

    <form id="bayarform" class="form-validation" method="POST" action="{{ route('directpayIndex') }}" enctype="multipart/form-data">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        
        <input type="hidden" name="desc" id="desc" value="Request_Help">
        
        <input type="hidden" name="amount" value="{{ $amount }}">
        <input type="hidden" name="request_id" value="{{ $request_id }}">
    </form>

</body>
</html>

