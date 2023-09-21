<!DOCTYPE html>
<html>
<head>
<style>
table, td, th {
  border: 1px solid;
}
td{
    text-align: center;
}
table {
    margin: auto;
  width: 80%;
  border-collapse: collapse;
}
body{
    margin: auto;
    border: 1px solid black;
}
</style>

    <title>List Passenger</title>
</head>
<body>
@foreach($data as $record)
   <center><h2>Passenger List for Trip Number : {{ $record->trip_number }}</h2></center> 
@break
@endforeach
   <table>
        <thead>
            <tr>
                <th>Ticket Number</th>
                <th>Passenger Name</th>
                <th>Ticket Booked Date</th>
                <th>Aboard</th>
            </tr>
        </thead>    
        <tbody>
            @foreach($data as $record)
                <tr>
                <td>T-{{ $record->trip_number }}-{{ $record->bookid }}{{ $record->available_seat }}{{ $record->booked_seat }}</td>
                <td>{{ $record->name }}</td>
                <td>{{ $record->book_date }}</td>
                <td> </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>