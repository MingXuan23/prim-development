@foreach ($comments as $comment)
    @include('social-media.components.comment', ['comment' => $comment])
@endforeach