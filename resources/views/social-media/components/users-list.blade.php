@foreach ($users as $user)
    @include('social-media.components.user-card', ['user' => $user])
@endforeach