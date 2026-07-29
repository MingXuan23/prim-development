@extends('layouts.social-media-layouts')

@section('css')
    <link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
    <style>
        .errorMessage {
            color: red;
        }

        .donation-cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            justify-content: center;
            gap: 10px;
        }
    </style>
@endsection

@section('content')
    <div class="p-4 d-flex flex-column align-items-center">
        @include('social-media.components.search-bar', ['searchUrl' => route('social-media.donationPostsIndex')])

        <div class="donation-cards" id="donations">
            @include('social-media.components.donation-posts-list', ['donations' => $donations])
        </div>

        <div id="loading">Loading...</div>

        {{-- share donation modal --}}
        <div id="share-donation-modal" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Kongsi</h4>
                    </div>
                    <form action="{{ route('social-media.addPost') }}" method="post">
                        {{ csrf_field() }}
                        <input type="hidden" id="modal-donation-id" name="source_id">
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
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            $("#loading").hide();

            $(document).on("click", ".share-donation-btn", function (e) {
                e.preventDefault();

                $("#share-donation-modal #modal-donation-name").val("");
                let donationId = $(this).closest(".donation-card").data("donationid");
                let donationName = $(this).closest(".donation-card-footer").find("#donation-name").text();
                $("#share-donation-modal #modal-donation-id").val(donationId);
                $("#share-donation-modal #modal-donation-name").val(donationName);
                $("#share-donation-modal").modal("show");
            });

            let currentPage = parseInt("{{ $donations->currentPage() }}");
            let isLoading = false;
            let hasMorePages = "{{ $donations->hasMorePages() ? 'true' : 'false' }}";

            $(window).scroll(function () {
                if (isLoading || !hasMorePages) return;

                if ($(window).scrollTop() + $(window).height() >= $(document).height() - 200) {
                    isLoading = true;
                    $("#loading").show();

                    $.ajax({
                        type: "GET",
                        url: "{{ route('social-media.donationPostsIndex') }}",
                        data: {
                            "search": $("#search-bar").val(),
                            "page": currentPage + 1,
                        },
                        success: function (response) {
                            if (!response || response.error) {
                                $(".errorMessage").text(response.error).show();
                                return;
                            }

                            $("#donations").append(response.html);
                            isLoading = false;
                            currentPage++;
                            hasMorePages = response.hasMorePages;
                            $("#loading").hide();
                        }
                    })
                }
            })
        });
    </script>
@endsection
