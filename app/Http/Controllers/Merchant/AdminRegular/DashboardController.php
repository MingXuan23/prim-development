<?php

namespace App\Http\Controllers\Merchant\AdminRegular;

use App\Http\Controllers\Merchant\RegularMerchantController;
use App\Models\Organization;
use App\Models\TypeOrganization;
use App\Models\PgngOrder;
use App\Http\Controllers\Controller;
use App\Http\Jajahan\Jajahan;
use Illuminate\Http\Request;
use App\Http\Requests\OrganizationRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Yajra\DataTables\DataTables;

class DashboardController extends Controller
{
    public function index()
    {   
        $merchant = RegularMerchantController::getAllMerchantOrganization();
        
        return view('merchant.regular.admin.dashboard.index', compact('merchant'));
    }

    public function edit($id)
    {
        $org = Organization::find($id);
        $states = Jajahan::negeri();

        return view('merchant.regular.admin.dashboard.edit', compact('org', 'states'));
    }

    public function update(OrganizationRequest $request)
    {
        if($this->orgIsExist($request->nama)) {
            return back()->withInput()->with('error', 'Nama Organisasi Telah Diambil');
        }

        Organization::where('id', $request->id)->update($request->validated());

        if (!is_null($request->organization_picture)) {
            $existing_picture = Organization::find($request->id)->organization_picture;

            $picture_name = $this->updateImage($request->organization_picture, $request->nama, $existing_picture);
            
            Organization::where('id', $request->id)->update([
                'organization_picture' => $picture_name
            ]);
        }

        if(isset($request->seller_id)) {
            Organization::where('id', $request->id)->update([
                'seller_id' => $request->seller_id,
                'fixed_charges' => $request->fixed_charges
            ]);
        }
        
        return back()->with('success', 'Berjaya Dikemaskini');
    }

    private function updateImage($requested_image, $org_name, $existing_image)
    {
        $file_name = NULL;

        // If item image exists
        if(!is_null($existing_image))
        {
            $file_name = $existing_image;
        }
        
        // If the admin want to change the image
        if (!is_null($requested_image)) {
            $link = explode(" ", $org_name);
            $str = implode("-", $link);
            // get existing image
            $file = public_path('/organization-picture/'.$existing_image);

            // if the existing image is exist then delete
            if(File::exists($file))
            {
                File::delete($file);
            }
            
            // store new image
            $extension = $requested_image->extension();
            $storagePath  = $requested_image->move(public_path('/organization-picture/'), $str.'.'.$extension);
            $file_name = basename($storagePath);
        }

        return $file_name;
    }

    private function orgIsExist($updated_org_name)
    {
        $type_org_id = TypeOrganization::where('nama', 'Peniaga Barang Umum')->first()->id;
        $org = Organization::where('type_org', $type_org_id)->select('nama')->get();

        foreach($org as $row) {
            if($row->nama == $updated_org_name) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function getLatestOrdersByNow(Request $request)
    {
        $org_id = $request->id;

        $order = DB::table('pgng_orders as pu')
                ->join('users as u', 'pu.user_id', 'u.id')
                ->whereIn('status', ["Paid"])
                ->where('organization_id', $org_id)
                ->whereBetween('pu.pickup_date', [Carbon::now()->startOfDay()->toDateTimeString(), Carbon::now()->endOfDay()->toDateTimeString()])
                ->select('pu.pickup_date', 'pu.total_price', 'pu.status', 'u.name', 'u.telno')
                ->orderBy('pickup_date', 'asc')
                ->get();
        
        if(request()->ajax()) 
        {   
            $table = Datatables::of($order);

            $table->addColumn('status', function ($row) {
                if ($row->status == "Paid") {
                    $btn = '<span class="badge rounded-pill bg-success text-white">Berjaya dibayar</span>';
                    return $btn;
                } else {
                    $btn = '<span class="badge rounded-pill bg-danger text-white">Tidak Diambil</span>';
                    return $btn;
                }
            });

            $table->editColumn('total_price', function ($row) {
                $total = number_format($row->total_price, 2, '.', '');
                return $total;
            });

            $table->editColumn('pickup_date', function ($row) {
                return Carbon::parse($row->pickup_date)->format('d/m/y h:i A');
            });

            $table->rawColumns(['total_price', 'status', 'pickup_date']);

            return $table->make(true);
        }
    }

    public function getAllTransaction(Request $request)
    {
        $org_id = $request->id;
        try {
            $transac = DB::table('pgng_orders as pu')
                    ->join('users as u', 'pu.user_id', 'u.id')
                    ->where('organization_id', $org_id)
                    ->whereIn('status', ['Paid', 'Picked-Up'])
                    ->select('u.name', 'pu.pickup_date', 'pu.total_price')
                    ->get();
            
            $org_name = Organization::find($org_id)->nama;
            
            return response()->json(['transac' => $transac, 'org_name' => $org_name]);
        } catch (\Throwable $th) {
            return $this->sendError($th->getMessage(), 500);
        }
    }

    public function getTotalOrder(Request $request)
    {
        $duration = $request->duration;
        $org_id = $request->id;

        if ($duration == "day") {
            try {
                $order = DB::table('pgng_orders')
                ->where('organization_id', $org_id)
                ->whereBetween('pickup_date', [Carbon::now()->startOfDay()->toDateTimeString(), Carbon::now()->endOfDay()->toDateTimeString()])
                ->whereIn('status', ['Paid', 'Picked-Up'])
                ->count();
                
                return response()->json(['order' => $order]);
            } catch (\Throwable $th) {
                return $this->sendError($th->getMessage(), 500);
            }
        } elseif ($duration == "week") {
            try {
                $order = DB::table('pgng_orders')
                ->where('organization_id', $org_id)
                ->whereBetween('pickup_date', [Carbon::now()->startOfWeek()->toDateTimeString(), Carbon::now()->endOfWeek()->toDateTimeString()])
                ->whereIn('status', ['Paid', 'Picked-Up'])
                ->count();
                
                return response()->json(['order' => $order]);
            } catch (\Throwable $th) {
                return $this->sendError($th->getMessage(), 500);
            }
        } elseif ($duration == "month") {
            try {
                $order = DB::table('pgng_orders')
                ->where('organization_id', $org_id)
                ->whereBetween('pickup_date', [Carbon::now()->startOfMonth()->toDateTimeString(), Carbon::now()->endOfMonth()->toDateTimeString()])
                ->whereIn('status', ['Paid', 'Picked-Up'])
                ->count();
                
                return response()->json(['order' => $order]);
            } catch (\Throwable $th) {
                return $this->sendError($th->getMessage(), 500);
            }
        }
    }

    public function getTotalIncome(Request $request)
    {
        $duration = $request->duration;
        $org_id = $request->id;
        
        if ($duration == "day") {
            try {
                $income = DB::table('pgng_orders')
                ->where('organization_id', $org_id)
                ->whereBetween('pickup_date', [Carbon::now()->startOfDay()->toDateTimeString(), Carbon::now()->endOfDay()->toDateTimeString()])
                ->whereIn('status', ['Paid', 'Picked-Up'])
                ->sum('total_price');

                $income = number_format($income, 2);
                
                return response()->json(['income' => $income]);
            } catch (\Throwable $th) {
                return $this->sendError($th->getMessage(), 500);
            }
        } elseif ($duration == "week") {
            try {
                $income = DB::table('pgng_orders')
                ->where('organization_id', $org_id)
                ->whereBetween('pickup_date', [Carbon::now()->startOfWeek()->toDateTimeString(), Carbon::now()->endOfWeek()->toDateTimeString()])
                ->whereIn('status', ['Paid', 'Picked-Up'])
                ->sum('total_price');

                $income = number_format($income, 2);
                
                return response()->json(['income' => $income]);
            } catch (\Throwable $th) {
                return $this->sendError($th->getMessage(), 500);
            }
        } elseif ($duration == "month") {
            try {
                $income = DB::table('pgng_orders')
                ->where('organization_id', $org_id)
                ->whereBetween('pickup_date', [Carbon::now()->startOfMonth()->toDateTimeString(), Carbon::now()->endOfMonth()->toDateTimeString()])
                ->whereIn('status', ['Paid', 'Picked-Up'])
                ->sum('total_price');

                $income = number_format($income, 2);
                
                return response()->json(['income' => $income]);
            } catch (\Throwable $th) {
                return $this->sendError($th->getMessage(), 500);
            }
        }
    }
}
