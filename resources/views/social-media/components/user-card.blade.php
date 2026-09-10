<style>
    .user-card {
        background-color: white;
        border-radius: 10px;
        padding: 25px;
        margin-bottom: 20px;
        max-width: 900px;
        width: 100%;
        display: flex;
        justify-content: space-between;
    }

    .user-card .user-info {
        color: gray;
    }

    .user-card .user-info:hover {
        color: gray;
        text-decoration: underline !important;
    }

    .user-card-left {
        display: flex;
        gap: 20px;
        align-items: center;
    }

    .user-card-right {
        display: flex;
        align-items: center;
    }

    h5 {
        color: black;
        font-weight: bold !important;
    }

    p {
        margin: 0 !important;
    }

    .profile-img {
        border-radius: 50%;
        max-width: 70px;
        max-height: 70px;
    }
</style>

<div class="user-card" data-userid="{{ $user->id }}">
    <a href="{{ route('social-media.profile', ['user_id' => $user->id]) }}" class="user-info">
        <div class="user-card-left">
            <img src="{{ isset($user->profile_image) ? URL::asset('uploads/profile_picture/' . $user->profile_image) : URL::asset('assets/images/users/user-4.jpg') }}" class="profile-img">
            <div>
                <h5>{{ $user->name }}</h5>
                <p>Admin kepada {{ $user->admin_count }} organisasi</p>
            </div>
        </div>
    </a>
    <div class="user-card-right">
        @if ($user->id == Auth::id())
            <a class="btn btn-primary view-profile-btn" href="{{ route('social-media.profile') }}">Lihat Profil</a>
        @elseif ($user->is_following)
            <button class="btn btn-secondary follow-btn">Telah Ikuti</button>
        @else
            <button class="btn btn-primary follow-btn">Ikuti</button>
        @endif
    </div>
</div>