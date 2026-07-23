@foreach ($posts as $post)
    @include('social-media.components.post', ['post' => $post])
@endforeach