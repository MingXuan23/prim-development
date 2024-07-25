@extends('layouts.master')

@section('content')
    <h1>Edit Referral Code Reward</h1>
    
    <form action="{{ route('referral-code-rewards.update', $reward) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ $reward->name }}" required>
        </div>
        <div class="form-group">
            <label for="desc">Description</label>
            <textarea class="form-control" id="desc" name="desc">{{ $reward->desc }}</textarea>
        </div>
        <div class="form-group">
            <label for="status">Status</label>
            <select class="form-control" id="status" name="status" required>
                <option value="1" {{ $reward->status ? 'selected' : '' }}>Active</option>
                <option value="0" {{ !$reward->status ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <div class="form-group">
            <label for="quantity">Quantity</label>
            <input type="number" class="form-control" id="quantity" name="quantity" value="{{ $reward->quantity }}" required>
        </div>
        <div class="form-group">
            <label for="payment">Payment</label>
            <select class="form-control" id="payment" name="payment" required>
                <option value="1" {{ $reward->payment ? 'selected' : '' }}>Yes</option>
                <option value="0" {{ !$reward->payment ? 'selected' : '' }}>No</option>
            </select>
        </div>
        <div class="form-group">
            <label for="paymentAmount">Payment Amount</label>
            <input type="number" step="0.01" class="form-control" id="paymentAmount" name="paymentAmount" value="{{ $reward->paymentAmount }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
@endsection