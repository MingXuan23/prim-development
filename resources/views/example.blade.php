@extends('layouts.master')

@section('css')
        <link href="{{ URL::asset('assets/libs/chartist/chartist.min.css')}}" rel="stylesheet" type="text/css" />
@endsection

@section('content')

@endsection


@section('script')
        <!-- Peity chart-->
        <script src="{{ URL::asset('assets/libs/peity/peity.min.js')}}"></script>

        <!-- Plugin Js-->
        <script src="{{ URL::asset('assets/libs/chartist/chartist.min.js')}}"></script>

        <script src="{{ URL::asset('assets/js/pages/dashboard.init.js')}}"></script>
@endsection
