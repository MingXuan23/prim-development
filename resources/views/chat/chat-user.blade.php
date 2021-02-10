@extends('layouts.master')

@section('content')
    <div class="container" style="margin-top: 10px;">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('List of Users') }}</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <div class="container">
                            @if ($users->count())
                                @foreach ($users as $user)
                                    <div class="row">
                                        <div class="nav-item dropdown">
                                            <a id="navbarDropdown" class="nav-link" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                                {{ $user->name }}
                                            </a>

                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                                <a class="dropdown-item" href="{{ route('chat-page', ['friendId' => $user->id]) }}">
                                                    {{ __('Chat with this Account') }}
                                                </a>
                                                @if ($user->parent_id != null)
                                                    <a class="dropdown-item" href="{{ route('chat-page', ['friendId' => $user->parent_id]) }}">
                                                        {{ __('Chat with Parent') }}
                                                    </a>
                                                    <a class="dropdown-item" href="{{ route('chat-page', ['friendId' => $user->id]) }}">
                                                        {{ __('Chat with Both') }}
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection