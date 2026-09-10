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
        <div id="notifications" class="w-100 d-flex flex-column align-items-center">
            @include('social-media.components.notifications-list', ['notifications' => $notifications])
        </div>

        <div id="loading">Loading...</div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            $(".errorMessage").hide();
            $("#loading").hide();

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
                });
            });

            let currentPage = parseInt("{{ $notifications->currentPage() }}");
            let isLoading = false;
            let hasMorePages = "{{ $notifications->hasMorePages() ? 'true' : 'false' }}";

            $(window).scroll(function () {
                if (isLoading || !hasMorePages) return;

                isLoading = true;
                $("#loading").show();

                $.ajax({
                    type: "GET",
                    url: "{{ route('social-media.notificationsIndex') }}",
                    data: {
                        page: currentPage + 1
                    },
                    success: function (response) {
                        if (!response || response.error) {
                            $(".errorMessage").text(response.error).show();
                            return;
                        }

                        $("#notifications").append(response.html);
                        isLoading = false;
                        $("#loading").hide();
                        hasMorePages = response.hasMorePages;
                        currentPage++;
                    }
                })
            })
        });
    </script>
@endsection