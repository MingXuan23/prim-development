@extends('layouts.social-media-layouts')

@section('css')
    <link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css"/>
    <style>
        .errorMessage {
            color: red;
        }

        #posts {
            width: 100%;
            max-width: 900px;
        }
    </style>
@endsection

@section('content')
    <div class="errorMessage"></div>

    <div class="p-4 d-flex flex-column align-items-center">
        @if (request()->is("*/saves*"))
            @include('social-media.components.search-bar', ['searchUrl' => route('social-media.saves')])
        @else
            @include('social-media.components.search-bar', ['searchUrl' => route('social-media.index')])
        @endif

        <div id="posts">
            @include('social-media.components.post-list', ['posts' => $posts])
        </div>

        <div id="post-loading">Loading...</div>

        @include('social-media.components.comment-modal')

        @include('social-media.components.share-post-modal')
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            $("#modal-alert").hide();
            $(".errorMessage").hide();
            $("#post-loading").hide();
            $("#comment-loading").hide();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr("content")
                }
            });

            let currentPostPage = parseInt("{{ $posts->currentPage() }}");
            let isPostLoading = false;
            let hasMorePostPages = "{{ $posts->hasMorePages() ? 'true' : 'false' }}";

            $(window).scroll(function () {
                if (isPostLoading || !hasMorePostPages) return;

                if ($(window).scrollTop() + $(window).height() >= $(document).height() - 200) {
                    isPostLoading = true;
                    $("#post-loading").show();

                    $.ajax({
                        type: "GET",
                        url: window.location.pathname.includes("/saves") ? "{{ route('social-media.saves') }}" : "{{ route('social-media.index') }}",
                        data: {
                            search: $("#search-bar").val(),
                            page: currentPostPage + 1
                        },
                        success: function (response) {
                            if (!response || response.error) {
                                $(".errorMessage").text(response.error).show();
                                return;
                            }

                            $("#posts").append(response.html);

                            isPostLoading = false;
                            $("#post-loading").hide();
                            currentPostPage++;
                            hasMorePostPages = response.hasMorePages;
                        }
                    })
                }
            });

            let currentCommentPage = 0;
            let isCommentLoading = false;
            let hasMoreCommentPages = true;
            let selectedPostId = null;

            $("#comment-modal .modal-body").on("scroll", function () {
                // check for comments scrolling
                let modalBody = $(this);

                if (isCommentLoading || !hasMoreCommentPages) return;

                if (modalBody.scrollTop() + modalBody.innerHeight() >= modalBody[0].scrollHeight - 50) {
                    isCommentLoading = true;
                    $("#comment-loading").show();
                    fetchComments(selectedPostId);
                }
            })

            $(document).on("click", ".like-btn", function (e) {
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

            $(document).on("click", ".save-btn", function (e) {
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

            $(document).on("click", ".comment-btn", function (e) {
                e.preventDefault();

                selectedPostId = $(this).parent().parent().data("postid");
                $("#comment-modal").modal("show");
                $("#comment-content").val("");
                $("#comments-section").empty();

                isCommentLoading = true;
                $("#comment-loading").show();
                currentCommentPage = 0;

                fetchPostById(selectedPostId);
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

                let postCommentBtn = $(this);
                postCommentBtn.prop("disabled", true);

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

                        $("#comments-section").prepend(response.html);
                        let commentsCount = parseInt($("#comments-count").text());
                        $("#comments-count").text(commentsCount + 1);
                        $(".post-card-footer[data-postid='" + selectedPostId + "']").find(".comment-btn p").text(commentsCount + 1);
                        $("#comment-content").val("");
                        $("#comment-media").val("");
                        closeMediaPreview();
                        postCommentBtn.prop("disabled", false);
                    }
                });
            });

            $(document).on("click", "#close-preview-btn", function (e) {
                e.preventDefault();

                closeMediaPreview();
                $("#comment-media").val("");
            });

            $(document).on("click", ".share-btn", function (e) {
                e.preventDefault();

                let postId = $(this).parent().parent().data("postid");
                $("#shared-post-id").val(postId);
                $("#share-post-modal").modal("show");

                fetchPostById(postId);
            });

            function closeMediaPreview() {
                $("#comment-media-preview").empty();
                $("#comment-media-preview").hide();
            }

            function resetCommentModal() {
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
                $("#comment-modal .shared-donation-card .donation-poster").prop("src", "");
                $("#comment-modal .shared-donation-card #donation-name").text("");
                $("#comment-modal .shared-donation-card #donate-now-btn").prop("href", "");
                $("#comment-modal .shared-donation-card").hide();
                $("#comment-modal .shared-post").hide();
            }

            function resetShareModal() {
                // reset modal fields
                $(".shared-post-author-name").text("");
                $(".shared-post-created-at").text("");
                $(".shared-post-content").text("");
                $(".shared-post-media").empty();
                $("#share-post-modal .shared-donation-card").hide();
            }

            function loadSharedPostData(sharedPost) {
                $(".shared-post-author-img").prop("src", sharedPost.user.profilePicture);
                $(".author-profile-link").prop("href", "/social-media/profile?user_id=" + sharedPost.user.id);
                $(".shared-post-author-name").text(sharedPost.user.name);
                $(".shared-post-created-at").text(sharedPost.created_at.replace("T", " ").split(".")[0]);
                $(".shared-post-content").text(sharedPost.content);
                $(".shared-post").show();

                if (sharedPost.media_type == "image") {
                    $(".shared-post-media").append("<img src='/uploads/post_media/" + sharedPost.media_url + "' class='post-media'>");
                } else if (sharedPost.media_type == "video") {
                    $(".shared-post-media").append("<video src='/uploads/post_media/" + sharedPost.media_url + "' class='post-media' controls muted></video>");
                }

                if (sharedPost.source_name && sharedPost.source_name.includes('Donation')) {
                    // shared donation in shared post in comment modal
                    $(".shared-post .shared-donation-card").show();
                    $(".shared-post .shared-donation-card .donation-poster").prop("src", "/donation-poster/" + sharedPost.source.donation_poster);
                    $(".shared-post .shared-donation-card #donation-name").text(sharedPost.source.nama);
                    $(".shared-post .shared-donation-card #donate-now-btn").prop("href", '/sumbangan_anonymous/' + (sharedPost.donation_share_url != null ? sharedPost.donation_share_url : sharedPost.source.url));
                }
            }

            function fetchPostById(postId) {
                resetCommentModal();
                resetShareModal();

                $.ajax({
                    type: "GET",
                    url: "{{ route('social-media.getPostByIdJson') }}",
                    data: {
                        "post_id": postId
                    },
                    success: function (response) {
                        if (response.error) {
                            $("#error-message").text(response.error).show();
                            return;
                        }

                        let postId = response.post.id;
                        let content = response.post.content;
                        let mediaType = response.post.media_type;
                        let mediaUrl = response.post.media_url;
                        let createdAt = response.post.created_at.replace("T", " ").split(".")[0];
                        let username = response.post.user.name;
                        let profilePicture = response.post.user.profile_image ? "/uploads/profile_picture/" + response.post.user.profile_image : "/assets/images/users/user-4.jpg";
                        let authorId = response.post.user.id;
                        let likesCount = response.post.likes_count;
                        let commentsCount = response.post.comments_count;
                        let sharesCount = response.post.shares_count;
                        let isLiked = response.post.is_liked;
                        let isSaved = response.post.is_saved;

                        let sharedPost = response.post.shared_post;

                        $(".shared-post-author-img").prop("src", profilePicture);
                        $(".author-profile-link").prop("href", "/social-media/profile?user_id=" + authorId);
                        $(".shared-post-author-name").text(username);
                        $(".shared-post-created-at").text(createdAt);
                        $(".shared-post-content").text(content);

                        // load share post data in share modal and comment modal
                        if (sharedPost && !$("#share-post-modal").hasClass("show")) {  // check if share post modal is not visible to prevent override of data in share post modal
                            loadSharedPostData(sharedPost);
                        }

                        // load data in comment modal
                        $("#comment-modal .modal-title").html(username + "'s post");

                        $("#comment-modal #author-profile-image").prop("src", profilePicture);
                        $("#comment-modal .modal-body #author-name").text(username);
                        $("#comment-modal .modal-body #created-at").text(createdAt);
                        $("#comment-modal .modal-body #post-content").text(content);
                        $("#comment-modal .modal-body #likes-count").text(likesCount);
                        $("#comment-modal .modal-body #comments-count").text(commentsCount);
                        $("#comment-modal .modal-body #shares-count").text(sharesCount);
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
                            $(".shared-post-media").append("<img src='/uploads/post_media/" + mediaUrl + "' class='post-media'>");
                        } else if (mediaType == "video") {
                            $("#comment-modal .modal-body #modal-media").html("<video src='uploads/post_media/" + mediaUrl + "' class='post-media' controls></video>");
                            $(".shared-post-media").append("<video src='/uploads/post_media/" + mediaUrl + "' class='post-media' controls muted></video>");
                        }

                        if (response.post.source_name && response.post.source_name.includes('Donation')) {
                            // shared donation poster in comment modal
                            $("#comment-modal .shared-donation-card").show();
                            $("#comment-modal .shared-donation-card .donation-poster").prop("src", "/donation-poster/" + response.post.source.donation_poster);
                            $("#comment-modal .shared-donation-card #donation-name").text(response.post.source.nama);
                            $("#comment-modal .shared-donation-card #donate-now-btn").prop("href", '/sumbangan_anonymous/' + (response.post.donation_share_url != null ? response.post.donation_share_url : response.post.source.url));

                            // shared donation poster in share modal
                            $("#share-post-modal .shared-donation-card").show();
                            $("#share-post-modal .shared-donation-card .donation-poster").prop("src", "/donation-poster/" + response.post.source.donation_poster);
                            $("#share-post-modal .shared-donation-card #donation-name").text(response.post.source.nama);
                            $("#share-post-modal .shared-donation-card #donate-now-btn").prop("href", '/sumbangan_anonymous/' + (response.post.donation_share_url != null ? response.post.donation_share_url : response.post.source.url));
                        }

                        fetchComments(postId);
                    }
                });
            }

            function fetchComments(postId = '') {
                // fetch comments
                $.ajax({
                    type: "GET",
                    url: "{{ route('social-media.getCommentsByPostIdJson') }}",
                    data: {
                        "post_id": postId,
                        "page": currentCommentPage + 1
                    },
                    success: function (response) {
                        if (response.error) {
                            $("#modal-alert").text(response.error).show();
                            return;
                        }

                        $("#comments-section").append(response.html);
                        isCommentLoading = false;
                        currentCommentPage++;
                        $("#comment-loading").hide();
                        hasMoreCommentPages = response.hasMorePages;
                    }
                });
            }
        });
    </script>
@endsection
