@extends('layouts.master')

@section('content')
  <div class="container">
    <h1>PRiM - Mata Ganjaran</h1>
    <h2>Tujuan</h2>
    <p>Untuk membolehkan ahli-ahli mendapat mata ganjaran apabila pembayaran dibuat menggunakan kod rujukan mereka. Mata ganjaran ini berguna untuk mengumpulkan faedah-faedah yang akan diumumkan pada masa akan datang.</p>
    <h2>Cara Menjadi Ahli</h2>

    <ol>
      <li>Klik <a href="{{route('register')}}">prim.my/register</a></li>
      <li>Selepas mendaftar, pergi ke <a href="{{route('profile.index')}}">prim.my/profile</a> dan aktifkan Prim - Mata Ganjaran, anda akan mendapat kod rujukan unik anda</li>
      <li>Kongsi sebarang derma di <a href="{{route('donate.index')}}">prim.my/derma</a> kepada orang lain</li>
      <li>Dapatkan mata ganjaran apabila anda atau orang lain menggunakan kod rujukan anda dalam derma</li>
    </ol>
    <h2>Level Keahlian</h2>
    <p>Secara asasnya, setiap ahli berada di peringkat 1. Untuk meningkatkan ke peringkat 2, anda perlu meneruskan aktiviti ini selama 40 hari:</p>
    <ul>
      <li>Anda membuat sekurang-kurangnya satu derma dalam satu hari</li>
      <li>Anda perlu kongsikan kod anda dan sekurang-kurangnya satu derma dalam satu hari</li>
      <li>Jumlah derma dengan kod anda perlu melebihi 16 kali</li>

    </ul>
  </div>

@endsection
