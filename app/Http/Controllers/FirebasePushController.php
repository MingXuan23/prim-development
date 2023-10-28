<?php

namespace App\Http\Controllers;
use Kreait\Laravel\Firebase\Facades\Firebase;    

use Illuminate\Http\Request;

class FirebasePushController extends Controller
{
    //
    protected $notification;
    public function __construct()
    {
        $this->notification = Firebase::messaging();
    }
}
