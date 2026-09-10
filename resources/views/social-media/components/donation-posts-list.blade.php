@if ($donations->count() == 0)
    <p>Tiada derma.</p>
@endif

@foreach ($donations as $donation)
    @include('social-media.components.donation-card', ['donation' => $donation])
@endforeach