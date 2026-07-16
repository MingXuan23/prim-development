@extends('layouts.social-media-layouts')

@section('css')
    <link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
    <style>
        .errorMessage {
            color: red;
        }

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

        .user-card a {
            color: gray;
        }

        .user-card a:hover {
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
@endsection

@section('content')
    <div class="errorMessage"></div>

    <div class="pt-4 d-flex flex-column align-items-center">
        @include('social-media.search-bar', ['searchUrl' => route('social-media.searchUserIndex')])

        <div id="users" class="w-100 d-flex flex-column align-items-center">
            @foreach ($users as $user)
                <div class="user-card" data-userid="{{ $user->id }}">
                    <a href="{{ route('social-media.profile', ['user_id' => $user->id]) }}">
                        <div class="user-card-left">
                            <img src="{{ isset($user->profile_image) ? URL::asset('uploads/profile_picture/' . $user->profile_image) : URL::asset('assets/images/users/user-4.jpg') }}" class="profile-img">
                            <div>
                                <h5>{{ $user->name }}</h5>
                                <p>Admin kepada {{ $user->admin_count }} organisasi</p>
                            </div>
                        </div>
                    </a>
                    <div class="user-card-right">
                        @if ($user->is_following)
                            <button class="btn btn-secondary follow-btn">Telah Ikuti</button>
                        @else
                            <button class="btn btn-primary follow-btn">Ikuti</button>
                        @endif
                    </div>
                </div>
            @endforeach
            {{ $users->links() }}
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
                        "followed_user_id": shareBtn.closest(".user-card").data("userid")
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