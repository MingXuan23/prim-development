@extends('layouts.master')
   
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <center>
                <button id="btn-nft-enable" onclick="initFirebaseMessagingRegistration()" class="btn btn-danger btn-xs btn-flat">Allow for Notification</button>
            </center>
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>
  
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
  
                    <form action="{{ route('send.notification') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" class="form-control" name="title">
                        </div>
                        <div class="form-group">
                            <label>Body</label>
                            <textarea class="form-control" name="body"></textarea>
                          </div>
                        <button type="submit" class="btn btn-primary">Send Notification</button>
                    </form>
  
                </div>
            </div>
        </div>
    </div>
</div>
  
<!-- Firebase App (the core Firebase SDK) is always required and must be listed first -->
<script src="https://www.gstatic.com/firebasejs/8.2.8/firebase-app.js"></script>

<!-- If you enabled Analytics in your project, add the Firebase SDK for Analytic -->
<script src="https://www.gstatic.com/firebasejs/8.2.8/firebase-analytics.js"></script>

<!-- Add Firebase products that you want to use -->
<script src="https://www.gstatic.com/firebasejs/8.2.8/firebase-auth.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.2.8/firebase-firestore.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.2.8/firebase-messaging.js"></script>

<script>
  
    var firebaseConfig = {
        apiKey: "AIzaSyDNqEXol-c8yRpS7Vrsha5H1WGLBaqfWbI",
        authDomain: "primmy.firebaseapp.com",
        projectId: "primmy",
        storageBucket: "primmy.appspot.com",
        messagingSenderId: "444112925702",
        appId: "1:444112925702:web:b18cfccc89f9835db27f87",
        measurementId: "G-DFHDL94FKJ",
    };
      
    firebase.initializeApp(firebaseConfig);
    const messaging = firebase.messaging();
  
    function initFirebaseMessagingRegistration() {
        console.log("init success");
            messaging
            .requestPermission()
            .then(function () {
                return messaging.getToken()
            })
            .then(function(token) {
                console.log('Token: ' + token);
   
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
  
                $.ajax({
                    url: '{{ route("save-token") }}',
                    type: 'POST',
                    data: {
                        token: token
                    },
                    dataType: 'JSON',
                    success: function (response) {
                        alert('Token saved successfully.');
                    },
                    error: function (err) {
                        console.log('User Chat Token Error '+ err);
                    },
                });
  
            }).catch(function (err) {
                console.log('User Chat Token Error '+ err);
            });
     }  
      
    messaging.onMessage(function(payload) {
        const noteTitle = payload.notification.title;
        const noteOptions = {
            body: payload.notification.body,
            icon: payload.notification.icon,
        };
        new Notification(noteTitle, noteOptions);
    });
   
</script>
@endsection