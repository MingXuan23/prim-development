@foreach ($donations as $donation)
    @include('social-media.components.donation-card', ['donation' => $donation])
@endforeach