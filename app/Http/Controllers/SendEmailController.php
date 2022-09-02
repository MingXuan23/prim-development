<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Routing\ResponseFactory;

use Illuminate\Http\Request;


use App\Mail\NotifyMail;

class SendEmailController extends Controller
{
    //
    public function index()
    {
        Mail::to('siowzheyi@gmail.com')->send(new NotifyMail());

        if (Mail::failures()) {
            dd("fail");
            return response()->Fail('Sorry! Please try again latter');
        } else {
            // return response()->success('Great! Successfully send in your mail');
            // dd("successs");
        }
    }

    public function sendMail()
    {
    }
}
