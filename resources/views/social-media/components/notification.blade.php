<div class="notification-card">
    @if ($notification->source_name == "likes")
        <i class="fas fa-heart" id="like-icon"></i>
        <div class="notification-info">
            <a href="{{ route('social-media.profile', ['user_id' => $notification->from_user_id]) }}">
                <h5>{{ $notification->content }}</h5>
            </a>
            <p class="text-gray">{{ $notification->created_at }}</p>
        </div>
    @elseif ($notification->source_name == "follows")
        <img src="{{ isset($notification->profile_image) ? URL::asset('uploads/profile_picture/' . $notification->profile_image) : URL::asset('assets/images/users/user-4.jpg') }}"
            class="notification-profile-img">
        <div class="notification-info">
            <a href="{{ route('social-media.profile', ['user_id' => $notification->follower_user_id]) }}">
                <h5>{{ $notification->content }}</h5>
            </a>
            <p class="text-gray">{{ $notification->created_at }}</p>
        </div>
    @endif
</div>