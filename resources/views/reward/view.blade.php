@extends('layouts.master')

@section('content')
<div class="container">
    <h1 class="mb-4">Available Rewards</h1>
    
    <div class="row row-cols-1 row-cols-md-2 g-4">
        @foreach($rewards as $reward)
        <div class="col">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">{{ $reward->name }}</h5>
                    <p class="card-text">{!! nl2br(e($reward->desc)) !!}</p>
                    <ul class="list-unstyled">
                        <li><strong>Quantity:</strong> {{ $reward->quantity }}</li>
                        @if($reward->payment)
                        <li><strong>Payment Amount:</strong> ${{ number_format($reward->paymentAmount, 2) }}</li>
                        @endif
                    </ul>
                </div>
                <div class="card-footer">
                    @if($reward->available)
                    <form action="" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-block">Claim Reward</button>
                    </form>
                    @else
                    <button class="btn btn-secondary btn-block" disabled>Not Available</button>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection

@push('styles')
<style>
    .card {
        transition: transform 0.2s;
    }
    .card:hover {
        transform: scale(1.03);
    }
    .btn-block {
        display: block;
        width: 100%;
    }
</style>
@endpush