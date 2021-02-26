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
        'https://uatdemofpx.paynet.com.my/UatBuyerBankSim1.7/CazhInter.jsp',
        'https://uat.mepsfpx.com.my/FPXMain/processMesgFromSBIBanks.jsp'
    ];
}
