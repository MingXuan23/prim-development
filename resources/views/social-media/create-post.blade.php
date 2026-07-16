@extends('layouts.social-media-layouts')

@section('css')
    <link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
    <style>
        .errorMessage {
            color: red;
        }

        #content {
            max-width: 70%;
            padding: 40px;
            margin: 0 auto;
        }

        .post-card {
            background-color: white;
            border-radius: 10px;
            padding: 25px 18px;
            margin-bottom: 20px;
            max-width: 900px;
        }

        textarea {
            resize: none !important;
        }
    </style>
@endsection

@section('content')
    <div id="content" class="p-4">
        <div class="post-card">
            <h4>Buat Post</h4>

            @if ($errors->any())
                <div class="alert alert-danger errorMessage">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('social-media.addPost') }}" method="post" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="form-group">
                    <label>Content</label>
                    <textarea name="content" class="form-control" rows="5"></textarea>
                </div>
                <div class="form-group">
                    <label>Media</label>
                    <input type="file" class="form-control" name="media" accept="image/*,video/*">
                </div>

                <div class="text-center">
                    <button class="btn btn-secondary" type="button" id="cancel">Batal</button>
                    <button class="btn btn-primary" type="submit">Muat Naik</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section("script")
    <script>
        $(document).ready(function () {
            $("#cancel").click(function () {
                window.location = "/social-media";
            })
        });
    </script>
@endsection