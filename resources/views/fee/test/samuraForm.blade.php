<!DOCTYPE html>
<html>
<head>
    <title>Upload Fee Assignment Data</title>
</head>
<body>

    @if(session('success'))
        <div style="color: green;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="color: red;">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div style="color: red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('fees.test.submitSamuraForm') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div>
            <label for="organization_id">Organization ID:</label>
            <input type="number" name="organization_id" id="organization_id" required>
        </div>

        <div>
            <label for="fee1_id">Fee 1 ID:</label>
            <input type="number" name="fee1_id" id="fee1_id" required>
        </div>

        <div>
            <label for="fee2_id">Fee 2 ID:</label>
            <input type="number" name="fee2_id" id="fee2_id" required>
        </div>

        <div>
            <label for="income_threshold">Income Threshold:</label>
            <input type="number" name="income_threshold" id="income_threshold" step="0.01" required>
        </div>

        <div>
            <label for="file">Upload Excel File:</label>
            <input type="file" name="file" id="file" accept=".xlsx" required>
        </div>

        <div>
            <button type="submit">Upload and Process</button>
        </div>
    </form>

</body>
</html>
