@extends('layouts.master')

@section('content')
    <h1>Referral Code Rewards</h1>
    <a href="{{ route('reward.create') }}" class="btn btn-primary">Create New Reward</a>
    
    <table class="table mt-3">
        <thead>
            <tr>
                <th>Name</th>
                <th>Status</th>
                <th>Quantity</th>
                <th>Payment</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rewards as $reward)
                <tr>
                    <td>{{ $reward->name }}</td>
                    <td>{{ $reward->status ? 'Active' : 'Inactive' }}</td>
                    <td>{{ $reward->quantity }}</td>
                    <td>{{ $reward->payment ? 'Yes' : 'No' }}</td>
                    <td>
                        <a href="{{ route('reward.edit', $reward) }}" class="btn btn-sm btn-primary">Edit</a>
                        <form action="{{ route('reward.destroy', $reward) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection