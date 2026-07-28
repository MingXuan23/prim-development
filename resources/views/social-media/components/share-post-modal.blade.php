<style>
    .shared-post {
        background-color: white;
        border-radius: 10px;
        padding: 25px 18px;
        margin-bottom: 20px;
        max-width: 900px;
        width: 100%;
    }

    .shared-post-header a {
        display: flex;
        gap: 10px;
        color: black;
        width: fit-content;
    }

    .shared-post-header a:hover {
        color: black;
    }
</style>

{{-- share modal --}}
<div id="share-post-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Kongsi</h4>
            </div>
            <form action="{{ route('social-media.addPost') }}" method="post">
                {{ csrf_field() }}
                <input type="hidden" id="shared-post-id" name="shared_post_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Content</label>
                        <textarea name="content" id="content" class="form-control" rows="5"></textarea>
                    </div>

                    <hr>

                    <div class="shared-post">
                        <div class="shared-post-header">
                            <a href="" id="author-profile-link">
                                <img src="" id="shared-post-author-img" class="profile-img">
                                <div>
                                    <h5 class="fw-bold text-black" id="shared-post-author-name"></h5>
                                    <p class="text-gray" id="shared-post-created-at"></p>
                                </div>
                            </a>
                        </div>

                        <p class="text-lg" id="shared-post-content"></p>

                        <div id="shared-post-media"></div>

                        <div class="shared-donation-card">
                            <img src="" class="donation-poster">
                            <h5 id="donation-name"></h5>
                            <a class="btn btn-primary" href="" target="_blank" id="donate-now-btn">Derma Sekarang</a>
                        </div>
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
{{-- end share modal --}}