<style>
    .post-card {
        background-color: white;
        border-radius: 10px;
        padding: 25px 18px;
        margin-bottom: 20px;
        max-width: 900px;
        width: 100%;
    }

    .post-card-header a {
        display: flex;
        gap: 10px;
        color: black;
        width: fit-content;
    }

    .post-card-header a:hover {
        color: black;
    }

    h5 {
        color: black;
        font-weight: bold !important;
    }

    .profile-img {
        border-radius: 50%;
        max-width: 50px;
        max-height: 50px;
    }

    .post-media {
        border-radius: 10px;
        margin: 10px auto;
        display: block;
        max-width: 100%;
        max-height: 500px;
    }

    .post-card-footer {
        margin-top: 10px;
        font-size: 16px;
        display: flex;
        justify-content: space-between;
    }

    .post-action-buttons {
        width: 30%;
        display: flex;
        justify-content: space-between;
    }

    .post-action-buttons a,
    .save-btn {
        cursor: pointer;
    }

    .text-gray {
        color: gray;
    }

    .shared-donation-card {
        max-width: 50%;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
        margin: 0 auto;
    }

    .shared-donation-card img {
        width: 100%;
        border-radius: 10px;
    }

    .shared-donation-card a {
        width: 100%;
    }

    .shared-post {
        background-color: white;
        border-radius: 10px;
        padding: 25px 18px;
        margin-bottom: 20px;
        max-width: 900px;
        width: 100%;
        border: 2px solid lightgray;
    }

    .shared-post-header a {
        display: flex;
        gap: 10px;
        color: black;
        width: fit-content;
    }

    .shared-post-header a:hover {
        color: black;
    }
</style>

<div class="post-card">
    <div class="post-card-header">
        <a href="{{ route('social-media.profile', ['user_id' => $post->user->id]) }}">
            <img src="{{ $post->user->profile_image ? URL::asset('uploads/profile_picture/' . $post->user->profile_image) : URL::asset('assets/images/users/user-4.jpg') }}" class="profile-img">
            <div>
                <h5 class="fw-bold text-black">{{ $post->user->name }}</h5>
                <p class="text-gray">{{ $post->created_at }}</p>
            </div>
        </a>
    </div>

    <p class="text-lg">{{ $post->content }}</p>

    @if($post->media_type == "image")
        <img src="{{ URL::asset('uploads/post_media/' . $post->media_url) }}" class="post-media">
    @elseif ($post->media_type == "video")
        <video src="{{ URL::asset('/uploads/post_media/' . $post->media_url) }}" class="post-media" controls muted></video>
    @elseif (isset($post->source))
        <div class="shared-donation-card">
            <img src="{{ URL::asset('donation-poster/' . $post->source->donation_poster) }}" class="donation-poster">
            <h5 class="text-center">{{ $post->source->nama }}</h5>
            <a class="btn btn-primary" href="{{ '/sumbangan_anonymous/' . (isset($post->donation_share_url) ? $post->donation_share_url : $post->source->url) }}" target="_blank">Derma
                Sekarang</a>
        </div>
    @elseif (isset($post->root_shared_post))
        <div class="shared-post">
            <div class="shared-post-header">
                <a href="{{ route('social-media.profile', ['user_id' => $post->root_shared_post->user->id]) }}">
                    <img src="{{ $post->root_shared_post->user->profile_image ? URL::asset('uploads/profile_picture/' . $post->root_shared_post->user->profile_image) : URL::asset('assets/images/users/user-4.jpg') }}"
                        class="profile-img">
                    <div>
                        <h5 class="fw-bold text-black">{{ $post->root_shared_post->user->name }}</h5>
                        <p class="text-gray">{{ $post->root_shared_post->created_at }}</p>
                    </div>
                </a>
            </div>

            <p class="text-lg">{{ $post->root_shared_post->content }}</p>

            @if($post->root_shared_post->media_type == "image")
                <img src="{{ URL::asset('uploads/post_media/' . $post->root_shared_post->media_url) }}" class="post-media">
            @elseif ($post->root_shared_post->media_type == "video")
                <video src="{{ URL::asset('/uploads/post_media/' . $post->root_shared_post->media_url) }}" class="post-media" controls muted></video>
            @elseif (isset($post->root_shared_post->source))
                <div class="shared-donation-card">
                    <img src="{{ URL::asset('donation-poster/' . $post->root_shared_post->source->donation_poster) }}" class="donation-poster">
                    <h5 class="text-center">{{ $post->root_shared_post->source->nama }}</h5>
                    <a class="btn btn-primary"
                        href="{{ '/sumbangan_anonymous/' . (isset($post->root_shared_post->donation_share_url) ? $post->root_shared_post->donation_share_url : $post->root_shared_post->source->url) }}"
                        target="_blank">Derma
                        Sekarang</a>
                </div>
            @endif
        </div>
    @endif

    <div class="post-card-footer" data-postid="{{ $post->id }}">
        <div class="post-action-buttons">
            <a class="text-danger like-btn">
                <i class="{{ $post->is_liked ? 'fas' : 'far' }} fa-heart" id="like-icon"></i>
                <p class="d-inline">{{ $post->likes_count }}</p>
            </a>
            <a class="text-primary comment-btn">
                <i class="far fa-comment"></i>
                <p class="d-inline">{{ $post->comments_count }}</p>
            </a>
            <a class="text-primary share-btn">
                <i class="fas fa-share"></i>
                <p class="d-inline">{{ $post->shares_count }}</p>
            </a>
        </div>

        <a class="text-primary save-btn">
            <i class="{{ $post->is_saved ? 'fas' : 'far' }} fa-bookmark" id="save-icon"></i>
        </a>
    </div>

    <hr>
</div>