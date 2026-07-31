<style>
    .comment {
        display: flex;
        gap: 10px;
    }

    .comment-content {
        background-color: lightgray;
        border-radius: 10px;
        color: black;
        padding: 10px;
    }

    .comment-content .comment-author-name {
        font-weight: bold;
    }

    .profile-img {
        border-radius: 50%;
        max-width: 50px;
        max-height: 50px;
    }

    .justify-end {
        justify-content: end;
    }
</style>

<div class="comment {{ $comment->user->id == Auth::id() ? 'justify-end' : '' }}">
    @if ($comment->user->id != Auth::id())
        <img src="{{ isset($comment->user->profile_image) ? URL::asset('uploads/profile_picture/' . $comment->user->profile_image) : URL::asset('assets/images/users/user-4.jpg') }}" class="profile-img">
    @endif

    <div class="comment-content">
        <h6 class="comment-author-name">{{ $comment->user->name }}</h6>
        <p>{{ $comment->content }}</p>
        @if ($comment->media_type == "image")
            <img src="{{ URL::asset('uploads/post_media/' . $comment->media_url) }}" class="post-media">
        @elseif ($comment->media_type == "video")
            <video src="{{ URL::asset('uploads/post_media/' . $comment->media_url) }}" class="post-media" controls></video>
        @endif
    </div>

    @if ($comment->user->id == Auth::id())
        <img src="{{ isset($comment->user->profile_image) ? URL::asset('uploads/profile_picture/' . $comment->user->profile_image) : URL::asset('assets/images/users/user-4.jpg') }}" class="profile-img">
    @endif
</div>