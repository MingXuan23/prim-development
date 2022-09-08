<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminMerchantController extends Controller
{
    private function getAdminOrganization()
    {
        DB::table('organization as o')
        ->join('organization_user as ou', 'ou.organization_id', '=', 'o.id')
        ->where([
            ['user_id', Auth::id()],
            ['role_id', 2015],
            ['status', 1],
            ['type_org', 2132],
            ['deleted_at', NULL],
        ])
        ->first();

    }
    /* START INDEX SECTION */
    public function index()
    {
        return view('merchant.admin.index');
    }
    /* END INDEX SECTION */

    /* START OPERATION HOURS SECTION */
    public function showOperationHours()
    {
        return view('merchant.admin.operation-hour.index');
    }

    public function editExistingOrder()
    {
        return view('merchant.admin.operation-hour.order');
    }
    /* END OPERATION HOURS SECTION */

    /* START PRODUCT DASHBOARD SECTION */
    public function showProductDashboard()
    {
        return view('merchant.admin.product.index');
    }

    public function showProductItem()
    {
        return view('merchant.admin.product.show');
    }
    /* END PRODUCT DASHBOARD SECTION */

    /* START ORDER SECTION */
    public function showAllOrder()
    {
        return view('merchant.admin.order.index');
    }

    public function showHistoryOrder()
    {
        return view('merchant.admin.order.history');
    }
    /* END ORDER SECTION */
}
