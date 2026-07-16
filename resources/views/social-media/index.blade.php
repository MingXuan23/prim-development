@extends('layouts.social-media-layouts')

@section('css')
    <link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
    <style>
        .errorMessage {
            color: red;
        }

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

        .social-media-buttons {
            width: 30%;
            display: flex;
            justify-content: space-between;
        }

        .social-media-buttons a,
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
    </style>
@endsection

@section('content')
    <div class="p-4 d-flex flex-column align-items-center">
        @include('social-media.search-bar', ['searchUrl' => route('social-media.index')])

        @foreach ($posts as $post)
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
                    <video src="{{ URL::asset('uploads/post_media/' . $post->media_url) }}" class="post-media" controls></video>
                @elseif (isset($post->shared_donation_id))
                    <div class="shared-donation-card">
                        <img src="{{ URL::asset('donation-poster/' . $post->donation_post->donation_poster) }}" class="donation-poster">
                        <h5>{{ $post->donation_post->nama }}</h5>
                        {{ $post->donation_share_url }}
                        <a class="btn btn-primary" href="{{ '/sumbangan_anonymous/' . (isset($post->donation_share_url) ? $post->donation_share_url : $post->donation_post->url) }}" target="_blank">Derma
                            Sekarang</a>
                    </div>
                @endif

                <div class="post-card-footer" data-postid="{{ $post->id }}">
                    <div class="social-media-buttons">
                        <a class="text-danger like-btn">
                            <i class="{{ $post->is_liked ? 'fas' : 'far' }} fa-heart" id="like-icon"></i>
                            <p class="d-inline">{{ $post->likes_count }}</p>
                        </a>
                        <a class="text-primary comment-btn">
                            <i class="far fa-comment"></i>
                            <p class="d-inline">{{ $post->comments_count }}</p>
                        </a>
                        <a class="text-primary">
                            <i class="fas fa-share"></i>
                            <p class="d-inline">0</p>
                        </a>
                    </div>

                    <a class="text-primary save-btn">
                        <i class="{{ $post->is_saved ? 'fas' : 'far' }} fa-bookmark" id="save-icon"></i>
                    </a>
                </div>
            </div>
        @endforeach

        @include('social-media.comment-modal')
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            $("#modal-alert").hide();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
                }
            });

            $(".like-btn").click(function (e) {
                e.preventDefault();
                let likeBtn = $(this);
                let postId = $(this).parent().parent().data("postid");

                $.ajax({
                    type: "POST",
                    url: "{{ route('social-media.toggleLike') }}",
                    data: {
                        "post_id": postId
                    },
                    success: function (response) {
                        likeBtn.find("p").text(response.updatedLikesCount);
                        likeBtn.find("i").toggleClass("far").toggleClass("fas");

                        if ($("#comment-modal").hasClass("show")) {
                            $(".post-card-footer[data-postid='" + postId + "']").find("#like-icon").toggleClass("far").toggleClass("fas");
                            $(".post-card-footer[data-postid='" + postId + "']").find(".like-btn p").text(response.updatedLikesCount);
                        }
                    }
                })
            });

            $(".save-btn").click(function (e) {
                e.preventDefault();
                let saveBtn = $(this);
                let postId = $(this).parent().data("postid");

                $.ajax({
                    type: "POST",
                    url: "{{ route('social-media.toggleSave') }}",
                    data: {
                        "post_id": postId
                    },
                    success: function (response) {
                        saveBtn.find("i").toggleClass("far").toggleClass("fas");

                        if ($("#comment-modal").hasClass("show")) {
                            $(".post-card-footer[data-postid='" + postId + "']").find("#save-icon").toggleClass("far").toggleClass("fas");
                        }
                    }
                })
            });

            $(".comment-btn").click(function (e) {
                e.preventDefault();

                let postId = $(this).parent().parent().data("postid");
                $("#comment-modal").modal("show");
                $("#comment-content").val("");
                fetchPostById(postId);
            });

            $("#comment-media-btn").click(function (e) {
                e.preventDefault();
                $("#comment-media").click();
            });

            $("#comment-media").change(function (e) {
                e.preventDefault();

                closeMediaPreview();

                let commentMediaPreview = $("#comment-media-preview");
                let media = $(this)[0].files[0];

                if (!media) {
                    return;
                }

                let mediaUrl = URL.createObjectURL(media);
                let mediaUploaded = "";

                if (media.type.startsWith("image/")) {
                    mediaUploaded = "<img src='" + mediaUrl + "' class='preview-media'>";
                } else if (media.type.startsWith("video/")) {
                    mediaUploaded = "<video src='" + mediaUrl + "' class='preview-media' controls></video>";
                }

                commentMediaPreview.show();
                commentMediaPreview.append("<button class='btn btn-secondary' id='close-preview-btn'><i class='fas fa-times'></i></button>");
                commentMediaPreview.append(mediaUploaded);
            });

            $("#post-comment-btn").click(function (e) {
                e.preventDefault();

                if ($("#comment-content").val() == "" && $("#comment-media")[0].files[0] == null) {
                    return;
                }

                let formData = new FormData();
                formData.append("post_id", $("#comment-modal .modal-body .post-card-footer").data("postid"));
                formData.append("comment", $("#comment-content").val());
                formData.append("media", $("#comment-media")[0].files[0] ?? null);

                $.ajax({
                    type: "POST",
                    url: "{{ route('social-media.addComment') }}",
                    processData: false,
                    contentType: false,
                    data: formData,
                    success: function (response) {
                        if (response.error) {
                            $("#modal-alert").text(response.error);
                            $("#modal-alert").show();
                            return;
                        }

                        addCommentCard(response.newComment);
                        let commentsCount = parseInt($("#comments-count").text());
                        let postId = $("#comment-modal .post-card-footer").data("postid");
                        $("#comments-count").text(commentsCount + 1);
                        $(".post-card-footer[data-postid='" + postId + "']").find(".comment-btn p").text(commentsCount + 1);
                        $("#comment-content").val("");
                        $("#comment-media").val("");
                        closeMediaPreview();
                    }
                });
            });

            $(document).on("click", "#close-preview-btn", function (e) {
                e.preventDefault();

                closeMediaPreview();
            });

            function closeMediaPreview() {
                $("#comment-media-preview").empty();
                $("#comment-media-preview").hide();
            }

            function fetchPostById(postId) {
                // reset modal fields
                $("#comment-modal #author-profile-image").prop("src", "");
                $("#comment-modal #author-name").text("");
                $("#comment-modal #created-at").text("");
                $("#comment-modal #post-content").text("");
                $("#comment-modal #likes-count").text("0");
                $("#comment-modal #comments-count").text("0");
                $("#comment-modal .post-footer").removeData("postid");
                $("#comment-modal #modal-media").empty();
                $("#comments-section").empty();
                $("#comment-modal .modal-body #like-icon").addClass("far").removeClass("fas");
                $("#comment-modal .modal-body #save-icon").addClass("far").removeClass("fas");
                $("#comment-modal .modal-body #modal-media").empty();

                $.ajax({
                    type: "GET",
                    url: "{{ route('social-media.getPostByIdJson') }}",
                    data: {
                        "post_id": postId
                    },
                    success: function (response) {
                        if (response.error) {
                            $("#error-message").show();
                            $("#error-message").text(response.error);
                            return;
                        }

                        let postId = response.post.id;
                        let content = response.post.content;
                        let mediaType = response.post.media_type;
                        let mediaUrl = response.post.media_url;
                        let createdAt = response.post.created_at.replace("T", " ").split(".")[0];
                        let likesCount = response.post.likes_count;
                        let commentsCount = response.post.comments_count;
                        let isLiked = response.post.is_liked;
                        let isSaved = response.post.is_saved;
                        let username = response.post.user.name;
                        let profilePicture = response.post.user.profile_image ? "/uploads/profile_picture/" + response.post.user.profile_image : "/assets/images/users/user-4.jpg";
                        let authorId = response.post.user.id;

                        $("#comment-modal .modal-title").html(username + "'s post");

                        $("#comment-modal #author-profile-image").prop("src", profilePicture);
                        $("#comment-modal .modal-body #author-name").text(username);
                        $("#comment-modal .modal-body #created-at").text(createdAt);
                        $("#comment-modal .modal-body #post-content").text(content);
                        $("#comment-modal .modal-body #likes-count").text(likesCount);
                        $("#comment-modal .modal-body #comments-count").text(commentsCount);
                        $("#comment-modal .modal-body .post-card-footer").data("postid", postId);
                        $("#comment-modal .modal-body .post-card-header a").prop("href", "/social-media/profile?user_id=" + authorId);

                        $(".post-card-footer[data-postid='" + postId + "']").find(".like-btn p").text(likesCount);
                        $(".post-card-footer[data-postid='" + postId + "']").find(".comment-btn p").text(commentsCount);

                        if (isLiked) {
                            $("#comment-modal .modal-body #like-icon").toggleClass("far").toggleClass("fas");
                        }

                        if (isSaved) {
                            $("#comment-modal .modal-body #save-icon").toggleClass("far").toggleClass("fas");
                        }

                        if (mediaType == "image") {
                            $("#comment-modal .modal-body #modal-media").html("<img src='uploads/post_media/" + mediaUrl + "' class='post-media'>");
                        } else if (mediaType == "video") {
                            $("#comment-modal .modal-body #modal-media").html("<video src='uploads/post_media/" + mediaUrl + "' class='post-media' controls></video>");
                        }

                        fetchComments(postId);
                    }
                });
            }

            function addCommentCard(comment) {
                // function to add comments to the UI
                let profileImagePath = comment.user.profile_image ? "uploads/profile_image/" + comment.user.profile_image : "assets/images/users/user-4.jpg";
                let mediaPath = comment.media_url == "" ? "" : "uploads/post_media/" + comment.media_url;
                let newComment = "<div class='comment'>" +
                    "<img src='" + profileImagePath + "' class='profile-img'>" +
                    "<div class='comment-content'>" +
                    "<h6 class='comment-author-name'>" + comment.user.name + "</h6>" +
                    "<p>" + (comment.content != null ? comment.content : "") + "</p>"

                if (comment.media_type == "image") {
                    newComment += "<img src='" + mediaPath + "' class='post-media'>";
                } else if (comment.media_type == "video") {
                    newComment += "<video src='" + mediaPath + "' class='post-media' controls></video>";
                }

                newComment += "</div></div>";

                $("#comments-section").prepend(newComment);
            }

            function fetchComments(postId) {
                $("#comments-section").empty();

                // fetch comments
                $.ajax({
                    type: "GET",
                    url: "{{ route('social-media.getCommentsByPostIdJson') }}",
                    data: {
                        "post_id": postId
                    },
                    success: function (response) {
                        if (response.error) {
                            $("#modal-alert").text(response.error);
                            $("#modal-alert").show();
                            return;
                        }

                        response.comments.forEach(comment => {
                            addCommentCard(comment);
                        });
                    }
                });
            }
        });
    </script>
@endsection