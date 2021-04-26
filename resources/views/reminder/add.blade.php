@extends('layouts.master')

@section('css')
<link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
{{-- @include('layouts.datepicker') --}}

@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Peringatan Derma</h4>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item active">Derma >> Peringatan Derma</li>
            </ol>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">
            @if(count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                    <li>{{$error}}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ $reminder->id == null ? route('reminder.store') :  action('ReminderController@update', $reminder->id) }}" method="POST">
            {{csrf_field()}}
            @isset($reminder->id)
            {{ method_field('PATCH')}}
            @endisset
            <div class="card-body">
                <div class="form-group">
                    <label>Derma</label>
                    <select name="donation" id="donation" class="form-control">
                        <option value="" >Semua Derma</option>
                        @foreach($donations as $donation)
                        <option value="{{ $donation->id ? $donation->id : old($donation->id)}}
                            " {{ old($donation->id) ? 'selected' : 'selected' }}>{{ $donation->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Peringatan</label>
                    <select type="text" name="recurrence" id="recurrence" class="form-control"
                        placeholder="Peringatan">
                        <option value="">Pilih Peringatan</option>
                        <option value="daily" {{ old('recurrence', $reminder->recurrence) == 'daily' ? 'selected' : '' }}>Harian</option>
                        <option value="weekly" {{ old('recurrence', $reminder->recurrence) == 'weekly' ? 'selected' : '' }}>Mingguan</option>
                        <option value="monthly" {{ old('recurrence', $reminder->recurrence) == 'monthly' ? 'selected' : '' }}>Bulanan</option>
                    </select>
                </div>
                <div class="form-group" id="date-form" style="{{ old('id', $reminder->id) ? '' : 'display: none'  }}">
                    <label class="control-label">Tarikh</label>
                    <select type="text" name="date" class="form-control" placeholder="Tarikh">
                        <option value="" disabled {{ old('id', $reminder->id) ? '' : 'selected'  }}>Pilih Tarikh</option>
                        @for ($i=1; $i <= 31;$i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="form-group" id="time-form" style="{{ old('id', $reminder->id) ? '' : 'display: none'  }}">
                    <label>Masa</label>
                    <div id="datepicker-time" class="input-group date">
                        <input class="form-control" id="time" name="time" type="time" placeholder="Pilih Masa"
                            autocomplete="off" value="{{ old('time', \Carbon\Carbon::parse($reminder->time)->format('H:i A')) }}">
                    </div>
                </div>
                <div class="form-group" id="day-form" style="{{ old('id', $reminder->id) ? '' : 'display: none'  }}">
                    <label>Hari</label>
                    <select type="text" name="day" class="form-control" placeholder="Peringatan">
                        <option value="" disabled {{ old('id', $reminder->id) ? '' : 'selected'  }}>Pilih Hari</option>
                        <option value="1" {{ old('time', $reminder->day) == '1' ? 'selected' : '' }}>Isnin</option>
                        <option value="2" {{ old('time', $reminder->day) == '2' ? 'selected' : '' }}>Selesa </option>
                        <option value="3" {{ old('time', $reminder->day) == '3' ? 'selected' : '' }}>Rabu </option>
                        <option value="4" {{ old('time', $reminder->day) == '4' ? 'selected' : '' }}>Khamis </option>
                        <option value="5" {{ old('time', $reminder->day) == '5' ? 'selected' : '' }}>Jumaat </option>
                        <option value="6" {{ old('time', $reminder->day) == '6' ? 'selected' : '' }}>Sabtu </option>
                        <option value="7" {{ old('time', $reminder->day) == '7' ? 'selected' : '' }}>Ahad </option>
                    </select>
                </div>
                <div class="form-group mb-0">
                    <div>
                        <button type="submit" class="btn btn-primary waves-effect waves-light mr-1" style="margin: 19px; float: right;">
                            Simpan
                        </button>
                    </div>
                </div>
            </div>
        </div>
        </form>
    </div>
</div>
</div>
@endsection


@section('script')
<!-- Peity chart-->
<script src="{{ URL::asset('assets/libs/peity/peity.min.js')}}"></script>

<!-- Plugin Js-->
<script src="{{ URL::asset('assets/libs/chartist/chartist.min.js')}}"></script>

<script src="{{ URL::asset('assets/js/pages/dashboard.init.js')}}"></script>

<!-- The core Firebase JS SDK is always required and must be listed first -->
<script src="https://www.gstatic.com/firebasejs/8.2.9/firebase-app.js"></script>

<script src="https://www.gstatic.com/firebasejs/8.2.9/firebase-messaging.js"></script>

<script>
    $(document).ready(function(){

        //reminder form
        $("#recurrence").change(function() {
            if ($(this).val() == "daily") {
                $('#time-form').show();
                $('#day-form').hide();
                $('#date-form').hide();
            } else if ($(this).val() == "weekly") {
                $('#day-form').show();
                $('#time-form').show();
                $('#date-form').hide();
            } else if ($(this).val() == "monthly") {
                $('#date-form').show();
                $('#time-form').show();
                $('#day-form').hide();
            }
        });

        //init notification and token for firebase services

        //firebase config
        var firebaseConfig = {
            apiKey: "AIzaSyDNqEXol-c8yRpS7Vrsha5H1WGLBaqfWbI",
            authDomain: "primmy.firebaseapp.com",
            projectId: "primmy",
            storageBucket: "primmy.appspot.com",
            messagingSenderId: "444112925702",
            appId: "1:444112925702:web:b18cfccc89f9835db27f87",
            measurementId: "G-DFHDL94FKJ"
        };
        
        firebase.initializeApp(firebaseConfig);
        const messaging = firebase.messaging();

        messaging
            .requestPermission()
            .then(function () {
                return messaging.getToken()
            })
            .then(function(token) {
                console.log(token);
   
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
                        // alert('Token saved successfully.');
                    },
                    error: function (err) {
                        // console.log('User Chat Token Error'+ err);
                    },
                });
  
            }).catch(function (err) {
                console.log('User Chat Token Error'+ err);
            });

            messaging.onMessage(function(payload) {
                const noteTitle = payload.notification.title;
                const noteOptions = {
                    body: payload.notification.body,
                    icon: payload.notification.icon,
                };
                new Notification(noteTitle, noteOptions);
            });
        
    });
</script>
@endsection