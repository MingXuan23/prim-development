@foreach ($notifications as $notification)
    @include('social-media.components.notification', ['notification' => $notification])
@endforeach