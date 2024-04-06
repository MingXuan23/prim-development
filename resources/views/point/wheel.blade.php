@extends('layouts.master')

@section('css')
<style>
    /* CSS for spinning wheel */
    #wheelContainer {
        position: relative;
        width: 400px;
        height: 400px;
        margin: 50px auto;
        border-radius: 50%;
        background-color: #333;
        overflow: hidden;
        /* Hide overflow to hide overflowing segments */
    }

    .segment {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        clip-path: polygon(50% 50%, 100% 0, 100% 100%);
        transform-origin: 50% 50%;
        background-color: transparent;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
        /* Add shadow for a 3D effect */
    }

    .segment:nth-child(odd) {
        background-color: #ff5722;
    }

    .segment:nth-child(even) {
        background-color: #ffc107;
    }

    .segment span {
        position: absolute;
        top: 50%;
        left: 75%;
        transform: translate(-50%, -50%);
        white-space: nowrap;
        font-size: 18px;
        /* Adjust font size */
        font-weight: bold;
        /* Make text bold */
        color: #fff;
        /* Set text color */
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        /* Add text shadow for better visibility */
    }

    .spinning-wheel-container {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
    }

    .arrow {
        position: absolute;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        width: 0;
        height: 0;
        border-left: 20px solid transparent;
        border-right: 20px solid transparent;
        border-top: 40px solid #000;
    }

    /* ... (rest of the CSS remains the same) */
</style>
@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18"></h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-body">
            
                <div class="spinning-wheel-container">
                <div class="arrow">
                        </div>
                    <div id="wheelContainer" class="relative">
                        
                        @foreach($referral_code as $code)
                        <div class="segment" style="transform: rotate({{ (360 / count($referral_code)) * $loop->index + 90 }}deg);">
                            <span>{{ $code->code }}</span>
                        </div>
                        @endforeach
                    </div>
                    <button id="spinButton" onclick="spinWheel()">Spin</button>
                    <div id="result" class="result"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    let isSpinning = false;

    window.addEventListener('DOMContentLoaded', () => {
        const segments = document.querySelectorAll('.segment');
        const segmentCount = segments.length;
        segments.forEach(segment => {
            segment.style.width = `${360 / segmentCount}%`;
        });
    });

    function spinWheel() {
    if (!isSpinning) {
        isSpinning = true;
        const wheel = document.getElementById('wheelContainer');
        const arrow = document.querySelector('.arrow');
        const spinButton = document.getElementById('spinButton');
        const result = document.getElementById('result');

        // Disable the spin button while spinning
        spinButton.disabled = true;

        const segments = Array.from(document.querySelectorAll('.segment'));
        const rotation = Math.floor(Math.random() * 360 * 5) + 3600; // Random rotation angle (5 to 10 spins)

        wheel.style.transition = 'transform 5s ease-in-out'; // Adjust animation duration
        wheel.style.transform = `rotate(${rotation}deg)`;

        // Calculate the final angle and determine the winning segment
        setTimeout(() => {
            const finalRotation = rotation % 360;
            let minDiff = Infinity;
            let winningSegmentIndex = 0;

            segments.forEach((segment, index) => {
                const segmentRotation = (360 / segments.length) * index; // Calculate segment rotation
                const diff = Math.abs(segmentRotation - finalRotation);

                if (diff < minDiff) {
                    minDiff = diff;
                    winningSegmentIndex = index;
                    console.log(winningSegmentIndex,diff);
                }
            });

            const winningSegment = segments[winningSegmentIndex];
            const winnerCode = winningSegment.querySelector('span').textContent;
            const winnerData = @json($referral_code).find(data => data.code === winnerCode);
            const winnerPointsInMonth = winnerData ? winnerData.points_in_month : 0;
            result.textContent = `The arrow points to: ${winnerCode} with ${winnerPointsInMonth} points this month!`;

            spinButton.disabled = false;
            isSpinning = false;
        }, 5000); // Wait for the animation to complete
    }
}


</script>
@endsection