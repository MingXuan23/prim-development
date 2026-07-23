@extends('layouts.social-media-layouts')

@section('css')
    <link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
    <style>
        .errorMessage {
            color: red;
        }
    </style>
@endsection

@section('content')
    <div class="errorMessage"></div>

    <div class="pt-4 d-flex flex-column align-items-center">
        @include('social-media.components.search-bar', ['searchUrl' => route('social-media.searchUserIndex')])

        <div id="users" class="w-100 d-flex flex-column align-items-center">
            @include('social-media.components.users-list', ['users' => $users])
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

            let currentPage = parseInt("{{ $users->currentPage() }}");
            let isLoading = false;
            let hasMorePages = "{{ $users->hasMorePages() ? 'true' : 'false' }}";

            $(window).scroll(function () {
                if (!hasMorePages || isLoading) return;

                if ($(window).scrollTop() + $(window).height() >= ($(document).height() - 200)) {
                    isLoading = true;
                    $("#loading").show();

                    $.ajax({
                        type: "GET",
                        url: "{{ route('social-media.searchUserIndex') }}",
                        data: {
                            "page": currentPage + 1,
                            "search": $("#search-bar").val()
                        },
                        success: function (response) {
                            if (!response || response.error) {
                                $(".errorMessage").text(response.error).show();
                                return;
                            }

                            $("#users").append(response.html);
                            currentPage++;
                            isLoading = false;
                            hasMorePages = response.hasMorePages;
                            $("#loading").hide();
                        }
                    });
                }
            });
        });
    </script>
@endsection