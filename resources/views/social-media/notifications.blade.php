@extends('layouts.social-media-layouts')

@section('css')
    <link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
    <style>
        .errorMessage {
            color: red;
        }

        .notification-card {
            background-color: white;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 20px;
            max-width: 900px;
            width: 100%;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .notification-card #like-icon {
            font-size: 30px;
            color: red;
        }

        .notification-info a:hover {
            text-decoration: underline !important;
            color: black;
        }

        h5 {
            color: black;
            font-weight: bold !important;
        }

        p {
            margin: 0 !important;
        }

        .notification-profile-img {
            border-radius: 50%;
            max-width: 70px;
            max-height: 70px;
        }
    </style>
@endsection

@section('content')
    <div class="errorMessage"></div>

    <div class="pt-4 d-flex flex-column align-items-center">
        <div id="users" class="w-100 d-flex flex-column align-items-center">
            @foreach ($notifications as $notification)
                <div class="notification-card">
                    @if ($notification->type == "like")
                        <i class="fas fa-heart" id="like-icon"></i>
                        <div class="notification-info">
                            <a href="{{ route('social-media.profile', ['user_id' => $notification->like->user_id]) }}">
                                <h5>{{ $notification->content }}</h5>
                            </a>
                            <p class="text-gray">{{ $notification->created_at }}</p>
                        </div>
                    @elseif ($notification->type == "follow")
                        <img src="{{ isset($notification->follow->follower->profile_image) ? URL::asset('uploads/profile_picture/' . $notification->follow->follower->profile_image) : URL::asset('assets/images/users/user-4.jpg') }}"
                            class="notification-profile-img">
                        <div class="notification-info">
                            <a href="{{ route('social-media.profile', ['user_id' => $notification->follow->follower_user_id]) }}">
                                <h5>{{ $notification->content }}</h5>
                            </a>
                            <p class="text-gray">{{ $notification->created_at }}</p>
                        </div>
                    @endif

                </div>
            @endforeach
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            $(".errorMessage").hide();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
                }
            });

            $(".follow-btn").click(function (e) {
                e.preventDefault();

                let shareBtn = $(this);

                $.ajax({
                    type: "POST",
                    url: "{{ route('social-media.followUser') }}",
                    data: {
                        "followed_user_id": shareBtn.closest(".notification-card").data("userid")
                    },
                    success: function (response) {
                        shareBtn.toggleClass("btn-primary").toggleClass("btn-secondary");

                        if (shareBtn.hasClass("btn-primary")) {
                            shareBtn.text("Ikuti");
                        } else {
                            shareBtn.text("Telah Ikuti");
                        }
                    }
                })
            });
        });
    </script>
@endsection