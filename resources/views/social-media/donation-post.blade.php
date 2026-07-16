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

        .donation-card {
            background-color: white;
            border-radius: 10px;
            padding: 25px 18px;
            margin-bottom: 20px;
            max-width: 400px;
            width: 100%;
        }

        .donation-card img {
            max-width: 350px;
            width: 100%;
            object-fit: cover;
            border-radius: 10px;
            min-height: 500px;
        }

        .donation-card-footer {
            text-align: center;
        }

        .donation-card-footer button,
        .donation-card-footer a {
            display: block;
            width: 100%;
            margin: 10px 0;
        }

        h5 {
            color: black;
            font-weight: bold !important;
            margin: 10px 0 !important;
        }
    </style>
@endsection

@section('content')
    <div class="p-4 d-flex flex-column align-items-center">
        @include('social-media.search-bar', ['searchUrl' => route('social-media.donationPostsIndex')])

        <div class="donation-cards">
            @foreach ($donations as $donation)
                <div class="donation-card" data-donationid="{{ $donation->id }}">
                    <img src="{{ URL::asset('donation-poster/' . $donation->donation_poster) }}" class="donation-poster" alt="{{ $donation->nama }}'s poster">
                    <div class="donation-card-footer">
                        <h5 class="mb-3" id="donation-name">{{ $donation->nama }}</h5>
                        <button class="btn btn-secondary share-btn">Kongsi</button>
                        <a href="/sumbangan_anonymous/{{ $donation->url }}" target="_blank" class="btn btn-primary">Derma Sekarang</a>
                    </div>
                </div>
            @endforeach

        </div>

        <div id="pagination-section" class="w-100 d-flex justify-content-center">
            {{ $donations->links() }}
        </div>

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
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function () {
            $(document).on("click", ".share-btn", function (e) {
                e.preventDefault();

                $("#share-donation-modal #modal-donation-name").val("");
                let donationId = $(this).closest(".donation-card").data("donationid");
                let donationName = $(this).closest(".donation-card-footer").find("#donation-name").text();
                $("#share-donation-modal #modal-donation-id").val(donationId);
                $("#share-donation-modal #modal-donation-name").val(donationName);
                $("#share-donation-modal").modal("show");
            });
        });
    </script>
@endsection