<style>
    .donation-card {
        background-color: white;
        border-radius: 10px;
        padding: 25px 18px;
        margin-bottom: 20px;
        max-width: 400px;
        width: 100%;
    }

    .donation-card img {
        max-width: 350px;
        width: 100%;
        object-fit: contain;
        border-radius: 10px;
        min-height: 500px;
    }

    .donation-card-footer {
        text-align: center;
    }

    .donation-card-footer button,
    .donation-card-footer a {
        display: block;
        width: 100%;
        margin: 10px 0;
    }

    h5 {
        color: black;
        font-weight: bold !important;
        margin: 10px 0 !important;
    }
</style>

<div class="donation-card" data-donationid="{{ $donation->id }}">
    <img src="{{ URL::asset('donation-poster/' . $donation->donation_poster) }}" class="donation-poster" alt="{{ $donation->nama }}'s poster">
    <div class="donation-card-footer">
        <h5 class="mb-3" id="donation-name">{{ $donation->nama }}</h5>
        <button class="btn btn-secondary share-donation-btn">Kongsi Untuk Sedekah Subuh</button>
        <a href="/sumbangan_anonymous/{{ $donation->url }}" target="_blank" class="btn btn-primary">Derma Sekarang</a>
    </div>
</div>