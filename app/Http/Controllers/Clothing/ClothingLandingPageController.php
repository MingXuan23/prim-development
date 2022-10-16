<?php

namespace App\Http\Controllers\Clothing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClothingLandingPageController extends Controller
{
    public function indexClothing()
    {
        return view('clothing.index');
    }

    public function getProductByTabbing(Request $request)
    {
        if ($request->ajax()) {
            $posters = '';

            if ($request->type == 0) {
                // $donations = DB::table('donations')
                //     ->where('lhdn_reference_code', '!=', null)
                //     ->where('donations.status', 1)
                //     ->inRandomOrder()
                //     ->get();
            }
            else
            {
                // $donations = DB::table('donations')
                //     ->where('donations.donation_type', $request->type)
                //     ->where('donations.status', 1)
                //     ->inRandomOrder()
                //     ->get();
            }

            // foreach ($donations as $donation) {
            //     $posters = $posters . '<div class="card"> <img class="card-img-top donation-poster" src="donation-poster/' . $donation->donation_poster . '" alt="Card image cap">';
            //     $posters = $posters . '<div class="card-body"><div class="d-flex flex-column justify-content-center ">';
            //     $posters = $posters . '<a href="' . route('URLdonate', ['link' => $donation->url]) . ' " class="boxed-btn btn-rounded btn-donation">' . ($donation->lhdn_reference_code == NULL ? "Derma Dengan Nama" : "Derma Pengecualian Cukai") .  '</a></div>';
            //     $posters = $posters . '<div class="d-flex justify-content-center"><a href="' . route('ANONdonate', ['link' => $donation->url]) . ' " class="boxed-btn btn-rounded btn-donation2">Derma Tanpa Nama</a></div></div></div>';
            // }

            if ($posters === '') {
                return '';
                // return '<div class="d-flex justify-content-center">Tiada Makulmat Dipaparkan</div>';
            }

            return $posters;
        }
    }

    public function urlProduct($link)
    {
        $user = "";
        
        //$donation = Donation::where('url', $link)->first();
        // $donation = DB::table('donations')
        //                 ->where('url', '=' , $link)
        //                 ->first();
        // dd($donation);

        // if($donation->status == 0)
        // {
        //     return view('errors.404');
        // }

        // if($donation->lhdn_reference_code != NULL)
        // {
        //     return view('paydonate.lhdn.index', compact('donation', 'user'));
        // }

        return view('clothing.pay');
    }
}
