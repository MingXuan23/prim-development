<style>
    .post-card-header a {
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

    #comments-modal {
        max-height: 700px;
    }

    #modal-footer {
        position: relative;
    }

    #comment-media-preview {
        position: absolute;
        left: 0;
        right: 0;
        bottom: 60px;
        width: 100%;
        padding: 20px;
        background-color: white;
        display: none;
        border-top: 2px solid lightgray;
        text-align: right;
    }

    #comment-media-preview .preview-media {
        width: 100%;
        object-fit: cover;
        border-radius: 10px;
    }

    #comment-actions {
        display: flex;
        gap: 10px;
        padding: 15px;
        align-items: center;
        border-top: 1px solid lightgray;
    }

    #comment-media-preview #close-preview-btn {
        margin-bottom: 10px;
    }

    #comments-section {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .profile-img {
        border-radius: 50%;
        max-width: 50px;
        max-height: 50px;
    }

    #modal-footer .profile-img,
    .comment .profile-img {
        width: 40px;
        height: 40px;
    }

    .comment {
        display: flex;
        gap: 10px;
    }

    .comment-content {
        background-color: lightgray;
        border-radius: 10px;
        color: black;
        padding: 10px;
    }

    .comment-content .comment-author-name {
        font-weight: bold;
    }

    .modal-title {
        color: black;
        width: 100%;
        text-align: center;
    }

    .modal-body {
        max-height: 500px;
        overflow: auto;
    }
</style>

{{-- comment modal --}}
<div id="comment-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <div id="modal-alert" class="alert alert-danger errorMessage"></div>

                <div class="post-card">
                    <div class="post-card-header">
                        <a href="">
                            <img src="" class="profile-img" id="author-profile-image">
                            <div>
                                <h5 class="fw-bold text-black" id="author-name"></h5>
                                <p class="text-gray" id="created-at"></p>
                            </div>
                        </a>
                    </div>

                    <p class="text-lg" id="post-content"></p>

                    <div id="modal-media"></div>

                    <div class="post-card-footer">
                        <div class="post-action-buttons">
                            <a class="text-danger like-btn">
                                <i class="far fa-heart" id="like-icon"></i>
                                <p class="d-inline" id="likes-count"></p>
                            </a>
                            <a class="text-primary comment-btn">
                                <i class="far fa-comment"></i>
                                <p class="d-inline" id="comments-count"></p>
                            </a>
                            <a class="text-primary">
                                <i class="fas fa-share"></i>
                                <p class="d-inline">0</p>
                            </a>
                        </div>

                        <a class="text-primary save-btn">
                            <i class="far fa-bookmark" id="save-icon"></i>
                        </a>
                    </div>
                </div>

                <hr>

                <div id="comments-section"></div>
            </div>

            <div id="modal-footer">
                <div id="comment-media-preview">
                    <button class="btn btn-secondary" id="close-preview-btn"><i class="fas fa-times"></i></button>
                </div>
                <div id="comment-actions">
                    <img src="" class="profile-img" id="author-profile-image">
                    <input type="text" placeholder="Tulis komen anda di sini..." class="form-control" id="comment-content" name="comment_content">
                    <input type="file" hidden accept="image/*,video/*" id="comment-media" name="comment_media">

                    <button class="btn" id="comment-media-btn"><i class="fas fa-image"></i></button>
                    <button class="btn btn-primary" id="post-comment-btn"><i class="fas fa-paper-plane"></i></button>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- end comment modal --}}