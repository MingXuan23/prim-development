<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LandingPageController extends Controller
{
    public function index()
    {
        return view('landing-page.index');
    }

    public function organizationList()
    {
        return view('landing-page.organization_list');
    }

    public function activitylist()
    {
        return view('landing-page.listactivity');
    }

    public function activitydetails()
    {
        return view('landing-page.activitydetails');
    }
}
