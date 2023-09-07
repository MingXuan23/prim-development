@extends('layouts.master')

@section('css')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
        <link rel="stylesheet" href="http://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
@include('layouts.datatable');

@endsection

@section('content')
<div class="row align-items-center">
    <div class="col-sm-6">
        <div class="page-title-box">
            <h4 class="font-size-18">Set Kenderaan Baru</h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
            <div class="flash-message"></div>
                <div class="table-responsive">
                    <form action="{{route('grab.insert')}}" method="post">
                         @if(Session::has('success'))
                         <div class="alert alert-success">{{Session::get('success')}}</div>
                         @endif
                         @if(Session::has('fail'))
                         <div class="alert alert-danger">{{Session::get('fail')}}</div>
                         @endif
                         @csrf
                        <div class="form-group">
                        <label>Jenama Kereta</label>
                        <input type="text" class="form-control" aria-describedby="emailHelp" placeholder="Car Brand" name="carbrand">
                        </div><br>
                        <div class="form-group">
                        <label>Nama Kereta</label>
                        <input type="text" class="form-control" aria-describedby="emailHelp" placeholder="Car Name" name="carname">
                        </div><br>
                        <div class="form-group">
                        <label>Nombor Plat Kenderaan</label>
                        <input type="text" class="form-control" aria-describedby="emailHelp" placeholder="Car Registration Number" name="carregisternumber">
                        </div><br>
                        <div class="form-group">
                        <label>Bilangan Tempat Duduk</label>
                        <select class="form-select" aria-label="Default select example"  name="totalseat">
                        <option selected disabled>Select Total Seat</option>
                        <option value="4">4 seater</option>
                        <option value="6">6 seater</option>
                        </select>
                        </div><br>
                        <div class="form-group">
                        @foreach($data as $rows)
                        <input type="text" class="form-control" aria-describedby="emailHelp" placeholder="Car Registration Number" name="organizationid" value ="{{ $rows->id }}" hidden>
                        @endforeach
                        </div>
                        <div class="form-group">
                        <input type="text" class="form-control" aria-describedby="emailHelp" placeholder="Car Registration Number" name="status" value ="NEW INSERT" hidden>
                        </div>
                        <div class="form-group">
                        <label>Masa </label>
                        <input type="time" class="form-control" placeholder="Available Time"  name="time">
                        </div><br>
                        <div class="form-group mb-0">
                        <div class="text-right">
                            <a type="button" href="{{ url()->previous() }}"
                                class="btn btn-secondary waves-effect waves-light mr-1">
                                Kembali
                            </a>
                            <button type="submit" class="btn btn-primary waves-effect waves-light mr-1">
                                Simpan
                            </button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection