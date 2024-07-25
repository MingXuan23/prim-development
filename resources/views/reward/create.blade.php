@extends('layouts.master')

@section('content')
    <h1>Create Referral Code Reward</h1>
    
    <form action="{{ route('reward.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="desc">Description</label>
            <textarea class="form-control" id="desc" name="desc"></textarea>
        </div>
       
        <div class="form-group">
            <label for="quantity">Quantity</label>
            <input type="number" class="form-control" id="quantity" name="quantity" required>
        </div>
        <div class="form-group">
            <label for="payment">Payment</label>
            <select class="form-control" id="payment" name="payment" required>
                <option value="1">Yes</option>
                <option value="0">No</option>
            </select>
        </div>
        <div class="form-group">
            <label for="paymentAmount">Payment Amount</label>
            <input type="number" step="0.01" class="form-control" id="paymentAmount" name="paymentAmount" required>
        </div>
        <button type="submit" class="btn btn-primary">Create</button>
    </form>
@endsection