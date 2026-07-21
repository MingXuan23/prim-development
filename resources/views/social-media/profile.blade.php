@extends('layouts.social-media-layouts')

@section('css')
    <link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- for input mask -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.4.0/css/bootstrap.min.css" />
    <style>
        /* ----------------------------------- profile styling ----------------------------------- */
        .profile-card {
            background-color: white;
            border-radius: 10px;
            margin: 20px auto;
            max-width: 900px;
            width: 100%;
        }

        .profile-card-header {
            overflow: hidden;
        }

        .user-info-section {
            padding: 20px 18px;
            display: flex;
            gap: 20px;
            color: black;
        }

        .user-info-section .personal-info {
            padding-top: 20px;
        }

        .profile-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 50px;
        }

        .profile-img-lg {
            border-radius: 50%;
            max-width: 120px;
            max-height: 120px;
        }

        .cover-img {
            width: 100%;
            border-radius: 10px 10px 0 0;
            display: block;
            max-height: 200px;
            object-fit: cover;
        }

        .profile-stats-section {
            display: flex;
            gap: 50px;
            margin: 20px 0;
        }

        .profile-stats-section button,
        .profile-stats-section a {
            height: fit-content;
        }

        .profile-stat {
            text-align: center;
        }

        .profile-stat .number {
            font-weight: bold;
            color: black;
            margin: 0;
        }

        .profile-stat .title {
            color: gray;
            margin: 0;
        }

        .horizontal-nav ul {
            list-style-type: none;
            display: flex;
            gap: 15px
        }

        .nav-item {
            cursor: pointer;
            padding: 15px 10px;
            position: relative;
        }

        .nav-item:hover {
            color: #4452cc;
        }

        .nav-item.active {
            color: #626ed4;
        }

        .nav-item.active::after {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            height: 4px;
            bottom: 0;
            background-color: #626ed4;
            border-radius: 8px;
        }

        .profile-body {
            padding: 15px 20px;
        }

        /* ----------------------------------- end profile styling ----------------------------------- */

        /* ----------------------------------- post styling ----------------------------------- */
        .post {
            padding: 10px 0;
        }

        h5 {
            color: black;
            font-weight: bold !important;
        }

        .post-header a {
            display: flex;
            gap: 10px;
            width: fit-content;
        }

        .post-profile-img {
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

        .post-footer {
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

        /* ----------------------------------- end post styling ----------------------------------- */

        #medias {
            display: flex;
            gap: 15px;
            width: 100%;
            flex-wrap: wrap;
        }

        #medias .media-post {
            width: 150px;
            height: 150px;
            object-fit: contain;
            border-radius: 10px;
            cursor: pointer;
            border: 1px solid lightgray;
        }

        #donations {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }

        #donations .donation-card {
            text-align: center;
            border-radius: 10px;
            width: 100%;
        }

        #donations .donation-card img {
            width: 100%;
            border-radius: 10px;
        }

        #donations .donation-card .donation-card-footer {
            padding: 15px 0;
        }

        #donations .donation-card .donation-card-footer a {
            width: 100%;
            color: white;
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
    @if($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <div class="alert alert-danger d-none" id="error-message">
        <p>{{ $message }}</p>
    </div>

    <div class="profile-card">
        <img src="{{ URL::asset('assets/landing-page/img/bg/breadcrumb-bg.jpg') }}" class="cover-img">

        <div class="profile-card-header">
            <div class="user-info-section">
                <img src="{{ isset($userData['profile_image']) ? URL::asset('uploads/profile_picture/' . $userData['profile_image']) : URL::asset('assets/images/users/user-4.jpg') }}"
                    class="profile-img-lg">

                <div class="personal-info">
                    <h3>{{ $userData["name"] }}</h3>
                </div>
            </div>

            <div class="profile-info">
                <div class="profile-stats-section">
                    <div class="profile-stat">
                        <p class="number" id="followers-count">{{ $userData["followers_count"] }}</p>
                        <p class="title">Pengikut</p>
                    </div>

                    <div class="profile-stat">
                        <p class="number" id="followed-users-count">{{ $userData["followed_users_count"] }}</p>
                        <p class="title">Mengikuti</p>
                    </div>

                </div>

                <div class="profile-btns">
                    @if ($userData["id"] != Auth::id())
                        <button class="btn btn-{{ ($userData['is_following'] ? 'secondary' : 'primary') }}" type="button" id="follow-btn">{{ ($userData['is_following'] ? 'Telah Ikuti' : 'Ikuti') }}</button>
                    @else
                        <a class="btn btn-primary text-white" id="user-donation-details" href="/point" target="_blank">Butiran Derma</a>
                        <button class="btn btn-success" id="invite-members-btn">Menjemput Ahli Baharu</button>
                    @endif
                </div>
            </div>
        </div>

        <nav class="horizontal-nav">
            <ul>
                <li class="nav-item active">Maklumat Pengguna</li>
                <li class="nav-item">Post</li>
                <li class="nav-item">Gambar</li>
                <li class="nav-item">Video</li>
                <li class="nav-item">Derma Saya</li>
            </ul>
        </nav>
    </div>

    <div class="profile-card">
        <div class="profile-body">
            <div id="about"></div>
            <div id="posts"></div>
            <div id="medias"></div>
            <div id="donations"></div>
        </div>
    </div>

    @include('social-media.comment-modal')

    {{-- share donation modal --}}
    <div id="share-donation-modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Kongsi</h4>
                </div>
                <form action="{{ route('social-media.addPost') }}" method="post">
                    {{ csrf_field() }}
                    <input type="hidden" id="modal-donation-id" name="shared_donation_id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nama Derma</label>
                            <input type="text" class="form-control" id="modal-donation-name" diabled>
                        </div>
                        <div class="form-group">
                            <label>Content</label>
                            <textarea name="content" id="content" class="form-control" rows="5"></textarea>
                        </div>
                    </div>

                    <hr>

                    <div id="modal-footer">
                        <div class="text-right p-2">
                            <button class="btn btn-primary" type="submit">Kongsi</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- end share donation modal --}}
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

            fetchDataByTabSelected($(".nav-item.active").text());

            $(".nav-item").click(function () {
                // remove active from all nav items class
                $(".nav-item").each(function (i, e) {
                    $(this).removeClass("active");
                });

                $("#about").hide();
                $("#posts").hide();
                $("#medias").hide();
                $("#donations").hide();

                // add active class to current clicked nav item
                $(this).addClass("active");

                fetchDataByTabSelected($(this).text())
            });

            function fetchDataByTabSelected(tabName) {
                switch (tabName) {
                    case "Maklumat Pengguna":
                        loadAboutSection();
                        break;
                    case "Post":
                        fetchPosts();
                        break;
                    case "Gambar":
                        fetchPhotoPosts();
                        break;
                    case "Video":
                        fetchVideoPosts();
                        break;
                    case "Derma Saya":
                        fetchDonation();
                        break;
                    default:
                        break;
                }
            }

            $("#follow-btn").click(function (e) {
                e.preventDefault();
                let followBtn = $(this);

                $.ajax({
                    type: "POST",
                    url: "{{ route('social-media.followUser') }}",
                    data: {
                        "followed_user_id": "{{ $userData['id'] }}"
                    },
                    success: function (response) {
                        if (response.error) {
                            displayError(response.error);
                            return;
                        }

                        followBtn.toggleClass("btn-primary").toggleClass("btn-secondary");

                        if (followBtn.hasClass("btn-secondary")) {
                            followBtn.text("Telah Ikuti")
                        } else {
                            followBtn.text("Ikuti");
                        }

                        $("#followers-count").text(response.followers_count);
                        $("#followed-users-count").text(response.followed_users_count);
                    }
                })
            })

            $(document).on("click", ".media-post", function (e) {
                e.preventDefault();

                fetchPostById($(this).data("postid"));
                $("#comment-modal").modal("show");
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

                if (mediaUploaded != "") {
                    commentMediaPreview.append("<button class='btn btn-secondary' id='close-preview-btn'><i class='fas fa-times'></i></button>");
                    commentMediaPreview.append(mediaUploaded);
                    commentMediaPreview.show();
                }
            });

            $("#invite-members-btn").click(function (e) {
                e.preventDefault();

                $.ajax({
                    type: "GET",
                    url: "{{ route('point.getReferralCode') }}",
                    data: {
                        "object": false,
                        "user_id": "{{ $userData['id'] }}"
                    },
                    success: function (response) {
                        navigator.clipboard.writeText("/register?referral_code=" + response.referral_code);
                        alert("Link copied to clipboard.");
                    }
                })
            })

            $(document).on("click", "#close-preview-btn", function (e) {
                e.preventDefault();

                closeMediaPreview();
                $("#comment-media").val("");
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
                        closeMediaPreview();
                        $("#comment-media").val("");
                    }
                });
            });

            $("#comment-media-btn").click(function (e) {
                e.preventDefault();
                $("#comment-media").click();
            });

            $(document).on("click", ".comment-btn", function (e) {
                e.preventDefault();

                fetchPostById($(this).parent().parent().data("postid"));
                $("#comment-modal").modal("show");
                $("#comment-content").val("");
            });

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
                });
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
                });
            });

            $(document).on("click", ".share-btn", function (e) {
                e.preventDefault();

                $("#share-donation-modal #modal-donation-name").val("");
                let donationId = $(this).closest(".donation-card").data("donationid");
                let donationName = $(this).closest(".donation-card-footer").find("#donation-name").text();
                console.log(donationId);
                $("#share-donation-modal #modal-donation-id").val(donationId);
                $("#share-donation-modal #modal-donation-name").val(donationName);
                $("#share-donation-modal").modal("show");
            });

            function displayError(message) {
                $("#error-message").show();
                $("#error-message").text(message);
            }

            function closeMediaPreview() {
                $("#comment-media-preview").empty();
                $("#comment-media-preview").hide();
            }

            function fetchPosts() {
                let postsDisplay = $("#posts");
                postsDisplay.show();
                postsDisplay.empty();

                $.ajax({
                    type: "GET",
                    url: "{{ route('social-media.getPostsByUserIdJson') }}",
                    data: {
                        "user_id": "{{ $userData['id'] }}"
                    },
                    success: function (response) {
                        if (response.error) {
                            displayError(response.error);
                            return;
                        }

                        if (response.posts.length == 0) {
                            postsDisplay.text("Tiada post.");
                            return;
                        }

                        response.posts.forEach(post => {
                            let mediaElement = "";

                            if (post.media_type == "image") {
                                mediaElement = "<img src='/uploads/post_media/" + post.media_url + "' class='post-media'>";
                            } else if (post.media_type == "video") {
                                mediaElement = "<video src='/uploads/post_media/" + (post.media_url) + "' class='post-media' controls></video>"
                            } else if (post.donation_post) {
                                mediaElement = "<div class='shared-donation-card'><img src='/donation-poster/" + post.donation_post.donation_poster + "' class='donation-poster'>" +
                                    "<h5>" + post.donation_post.nama + "</h5>" +
                                    "<a class='btn btn-primary' href='/sumbangan_anonymous/" + post.donation_post.url + "' target='_blank'>Derma Sekarang</a>";
                            }

                            let postHtml = "<div class='post'><div class='post-header'>" +
                                "<a><img src='" + (post.user.profile_image ? "/uploads/profile_picture/" + post.user.profile_image : "/assets/images/users/user-4.jpg") + "' class='post-profile-img'>" +
                                "<div><h5>" + post.user.name + "</h5><p>" + post.created_at.replace("T", " ").split(".")[0] + "</p></div></a></div>" +
                                "<div class='post-body'><p>" + (post.content ? post.content : "") + "</p>" + mediaElement
                                + "</div><div class='post-card-footer' data-postid='" + post.id + "'>" +
                                "<div class='post-action-buttons'><a class='text-danger like-btn'><i class='" + (post.is_liked ? "fas" : "far") + " fa-heart' id='like-icon'></i> <p class='d-inline'>" + post.likes_count + "</p></a>" +
                                "<a class='text-primary comment-btn'><i class='far fa-comment'></i> <p class='d-inline'>" + post.comments_count + "</p></a>" +
                                "<a class='text-primary share-btn'><i class='fas fa-share'></i> <p class='d-inline'>0</p></a></div>" +
                                "<a class='text-primary save-btn'><i class='" + (post.is_saved ? "fas" : "far") + " fa-bookmark' id='save-icon'></i></a></div></div><hr>";

                            postsDisplay.append(postHtml);
                        })
                    }
                })
            }

            function fetchDonation() {
                let donationsDisplay = $("#donations");
                donationsDisplay.show();
                donationsDisplay.empty();

                $.ajax({
                    type: "GET",
                    url: "{{ route('social-media.getDonationsByUserIdJson') }}",
                    data: {
                        "user_id": "{{ $userData['id'] }}"
                    },
                    success: function (response) {
                        if (response.error) {
                            displayError(response.error);
                            return;
                        }

                        if (response.donations.length == 0) {
                            donationsDisplay.text("Tiada derma aktif.");
                            return;
                        }

                        response.donations.forEach(donation => {
                            let donationHtml = "<div class='donation-card' data-donationid='" + donation.id + "'>" +
                                "<img src='/donation-poster/" + donation.donation_poster + "'/>" +
                                "<div class='donation-card-footer'><h5 class='mb-3' id='donation-name'>" + donation.nama + "</h5>" +
                                "<button class='btn btn-secondary w-100 mb-2 share-btn'>Kongsi</button>" +
                                "<a class='btn btn-primary' href='/sumbangan_anonymous/" + donation.url + "' target='_blank'>Derma Sekarang</a></div></div>";

                            donationsDisplay.prepend(donationHtml);
                        });
                    }
                });
            }

            function fetchPhotoPosts() {
                let photoSection = $("#medias");
                photoSection.empty();
                photoSection.show();

                $.ajax({
                    type: "GET",
                    url: "{{ route('social-media.getPhotoPostsByUserIdJson') }}",
                    data: {
                        "user_id": "{{ $userData['id'] }}"
                    },
                    success: function (response) {
                        if (response.error) {
                            displayError(response.error);
                            return;
                        }

                        if (response.posts.length == 0) {
                            photoSection.prepend("<h4 class='text-center w-100'>Tiada post.</h4>");
                            return;
                        }

                        response.posts.forEach(post => {
                            photoSection.prepend("<img src='/uploads/post_media/" + post.media_url + "' class='media-post' data-postid='" + post.id + "'>");
                        });
                    }
                });
            }

            function fetchVideoPosts() {
                let photoSection = $("#medias");
                photoSection.empty();
                photoSection.show();

                $.ajax({
                    type: "GET",
                    url: "{{ route('social-media.getVideoPostsByUserIdJson') }}",
                    data: {
                        "user_id": "{{ $userData['id'] }}"
                    },
                    success: function (response) {
                        if (response.error) {
                            displayError(response.error);
                            return;
                        }

                        if (response.posts.length == 0) {
                            photoSection.prepend("<h4 class='text-center w-100'>Tiada video.</h4>");
                            return;
                        }

                        response.posts.forEach(post => {
                            photoSection.prepend("<video src='/uploads/post_media/" + post.media_url + "' class='media-post' data-postid='" + post.id + "'></video>");
                        });
                    }
                });
            }

            function fetchPostById(postId) {
                $("#comment-modal #author-profile-image").prop("src", "");
                $("#comment-modal #author-name").text("");
                $("#comment-modal #created-at").text("");
                $("#comment-modal #post-content").text("");
                $("#comment-modal #likes-count").text("0");
                $("#comment-modal #comments-count").text("0");
                $("#comment-modal .post-card-footer").removeData("postid");
                $("#comment-modal #modal-media").empty();
                $("#comments-section").empty();
                $("#comment-modal .modal-body #like-icon").addClass("far").removeClass("fas");
                $("#comment-modal .modal-body #save-icon").addClass("far").removeClass("fas");
                $("#comment-modal .shared-donation-card").hide();

                $.ajax({
                    type: "GET",
                    url: "{{ route('social-media.getPostByIdJson') }}",
                    data: {
                        "post_id": postId
                    },
                    success: function (response) {
                        if (response.error) {
                            displayError(response.error);
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

                        if (isLiked) {
                            $("#comment-modal .modal-body #like-icon").toggleClass("far").toggleClass("fas");
                        }

                        if (isSaved) {
                            $("#comment-modal .modal-body #save-icon").toggleClass("far").toggleClass("fas");
                        }

                        $("#comment-modal .modal-title").html(username + "'s post");

                        $("#comment-modal #author-profile-image").prop("src", profilePicture);
                        $("#comment-modal .modal-body #author-name").text(username);
                        $("#comment-modal .modal-body #created-at").text(createdAt);
                        $("#comment-modal .modal-body #post-content").text(content);
                        $("#comment-modal .modal-body #likes-count").text(likesCount);
                        $("#comment-modal .modal-body #comments-count").text(commentsCount);
                        $("#comment-modal .modal-body .post-card-footer").data("postid", postId);

                        if (response.post.donation_post) {
                            // shared donation poster
                            $("#comment-modal .shared-donation-card").show();
                            $("#comment-modal .shared-donation-card .donation-poster").prop("src", "/donation-poster/" + response.post.donation_post.donation_poster);
                            $("#comment-modal .shared-donation-card #donation-name").text(response.post.donation_post.nama);
                            $("#comment-modal .shared-donation-card #donate-now-btn").prop("href", '/sumbangan_anonymous/' + (response.post.donation_share_url != null ? response.post.donation_share_url : response.post.donation_post.url));
                        }

                        $(".post-card-footer[data-postid='" + postId + "']").find(".like-btn p").text(likesCount);
                        $(".post-card-footer[data-postid='" + postId + "']").find(".comment-btn p").text(commentsCount);

                        if (mediaType == "image") {
                            $("#comment-modal .modal-body #modal-media").html("<img src='/uploads/post_media/" + mediaUrl + "' class='post-media'>");
                        } else if (mediaType == "video") {
                            $("#comment-modal .modal-body #modal-media").html("<video src='/uploads/post_media/" + mediaUrl + "' class='post-media' controls></video>");
                        }

                        fetchComments(postId);
                    }
                });
            }

            function fetchComments(postId) {
                $.ajax({
                    type: "GET",
                    url: "{{ route('social-media.getCommentsByPostIdJson') }}",
                    data: {
                        "post_id": postId
                    },
                    success: function (response) {
                        if (response.error) {
                            $("#modal-alert").show();
                            $("#modal-alert").text(response.error);
                            return;
                        }

                        response.comments.forEach(comment => {
                            addCommentCard(comment);
                        });
                    }
                });
            }

            function addCommentCard(comment) {
                // function to add comments to the UI
                let profileImagePath = comment.user.profile_image ? "/uploads/profile_picture/" + comment.user.profile_image : "/assets/images/users/user-4.jpg";
                let mediaPath = comment.media_url == "" ? "" : "/uploads/post_media/" + comment.media_url;
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

            function loadAboutSection() {
                $("#about").show();
                $("#about").empty();

                // email
                let emailHtml = "<div class='form-group'><label>Emel</label><input type='text' class='form-control' readonly value='" + "{{ $userData['email'] }}" + "'></div>";

                // number of organizations involved in prim
                let noOfOrgsInvolvedHtml = "<div class='form-group'><label>Jumlah Organisasi Terlibat</label><input type='text' class='form-control' readonly value='" + "{{ $userData['organization_count'] }}" + "'></div>";

                $("#about").append(emailHtml);
                $("#about").append(noOfOrgsInvolvedHtml);
            }
        });
    </script>
@endsection