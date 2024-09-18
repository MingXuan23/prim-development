<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Indicates whether the XSRF-TOKEN cookie should be set on the response.
     *
     * @var bool
     */
    protected $addHttpCookie = true;

    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //
        'https://uat.mepsfpx.com.my/*',
        'https://uat.mepsfpx.com.my',
        'https://prim.my/transactionReceipt',
        'https://prim.my/paymentStatus',
        'https://dev.prim.my/devtrans',
        'https://prim.my/mobile/*',
        'https://dev.prim.my/mobile/*',
        'https://prim.my/directpayReceipt',
        'https://prim.my/api/derma/returnDermaView',
        // 'http://localhost:8000/api/derma/returnDermaView',
        // 'https://prim.my/sumbangan_anonymous/*',
        // 'https://prim.my/sumbangan/*',
        // 'http://localhost:8000/sumbangan_anonymous/*',
        // 'http://localhost:8000/sumbangan/*'

    ];
}
