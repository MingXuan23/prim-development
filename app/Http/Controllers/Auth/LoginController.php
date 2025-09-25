<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\User;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    // Login attempt
    // protected $maxAttempts = 3;

    // Denied login in minutes
    // protected $decayMinutes = 3;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            // Check if the current route is 'logout'
            if ($request->route()->getName() === 'logout') {
                // Allow the logout route to proceed without redirection
                return $next($request);
            }

            // If the user is authenticated, redirect them
            if (Auth::check()) {
                return redirect($this->redirectTo)->send();
            }

            return $next($request);
        });

        // Uncomment this if you want to apply 'guest' middleware except for logout
        // $this->middleware('guest')->except('logout');
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        Auth::logout();
        session()->forget('referral_code');
        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/derma');
    }

    public function startsWith($string, $startString)
    {
        $len = strlen($startString);
        return (substr($string, 0, $len) === $startString);
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        // $request->get  get email (name) from form
        if (session()->has('intendedUrl')) {
            $intendedUrl = session()->pull('intendedUrl');
            $this->redirectTo = $intendedUrl;

            session()->forget('intendedUrl');
        }

        $phone = $request->get('email');

        if (is_numeric($request->get('email'))) {
            $user = User::where('icno', $phone)->first();

            if ($user) {
                //dd($user);
                return ['icno' => $phone, 'password' => $request->get('password')];
            }
            if (!$this->startsWith((string)$request->get('email'), "+60") && !$this->startsWith((string)$request->get('email'), "60")) {
                if (strlen((string)$request->get('email')) == 10) {
                    $phone = str_pad($request->get('email'), 12, "+60", STR_PAD_LEFT);
                } elseif (strlen((string)$request->get('email')) == 11) {
                    $phone = str_pad($request->get('email'), 13, "+60", STR_PAD_LEFT);
                }
            } else if ($this->startsWith((string)$request->get('email'), "60")) {
                if (strlen((string)$request->get('email')) == 11) {
                    $phone = str_pad($request->get('email'), 12, "+", STR_PAD_LEFT);
                } elseif (strlen((string)$request->get('email')) == 12) {
                    $phone = str_pad($request->get('email'), 13, "+", STR_PAD_LEFT);
                }
            }
            return ['telno' => $phone, 'password' => $request->get('password')];
        } else if (strpos($request->get('email'), "@") !== false) {
            return ['email' => $request->get('email'), 'password' => $request->get('password')];
        } else {
            return ['telno' => $phone, 'password' => $request->get('password')];
        }
        // //elseif (filter_var($request->get('email'), FILTER_VALIDATE_EMAIL)) {
        //     return ['email' => $request->get('email'), 'password'=>$request->get('password')];
        // }
        return ['email' => $request->get('email'), 'password' => $request->get('password')];
    }

    public static function loginAsGuest()
    {
        $guest = DB::table('users as u')
            ->join('model_has_roles as m', 'm.model_id', 'u.id')
            ->where('m.role_id', 22)
            ->orderBy('u.updated_at')
            ->orderBy('u.id')
            ->select('u.id as guest_id')
            ->first();
        DB::table('users')->where('id', $guest->guest_id)->update([
            'updated_at' => now()
        ]);
        $cart = DB::table('pgng_orders')->where('user_id', $guest->guest_id)->where('status', 'In cart')->delete();
        //dd($cart);

        // DB::table('users')->update([
        //     'updated_at'=>now()
        // ]);
        Auth::loginUsingId($guest->guest_id);
        //dd($guestList);

    }
}
