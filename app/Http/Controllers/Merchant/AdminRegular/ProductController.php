<?php

namespace App\Http\Controllers\Merchant\AdminRegular;

use App\Http\Controllers\Merchant\RegularMerchantController;
use App\Models\Organization;
use App\Models\ProductItem;
use App\Models\ProductGroup;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Yajra\DataTables\DataTables;

class ProductController extends Controller
{
    public function indexProductGroup()
    {
        $merchant = RegularMerchantController::getAllMerchantOrganization();

        return view('merchant.regular.admin.product.index', compact('merchant'));
    }

    public function getAllProductGroup(Request $request)
    {
        $link = '';
        $org_id = $request->id;
        $group = ProductGroup::where('organization_id', $org_id)->get();
        foreach($group as $row)
        {
            $link .= '<a href="'.route('admin-reg.product-item', $row->id).'" class="list-group-item list-group-item-action flex-column">
            <div class="d-flex" >
            <div class="justify-content-start align-self-center"><p class="h4 mb-0">'.$row->name.'</p>
            </div>
            <div class="arrow-icon ml-auto justify-content-end align-self-center mb-0">
            <p class="h4 mb-0"><i class="fas fa-angle-right"></i></p>
            </div>
            </div>
            </a>';
        }
        return response()->json(['response' => $link]);
    }

    public function storeProductGroup(Request $request)
    {
        $org_id = $request->id;
        # Insert Product Group Data
        $pg = ProductGroup::create([
            'name' => $request->name,
            'organization_id' => $org_id,
        ]);

        if($pg) {
            return response()->json(['message' => 'Berjaya Tambah Jenis Produk']);
        } else {
            return response()->json(['message' => 'ERROR']);
        }
    }

    public function showProductItem($id)
    {
        $group = ProductGroup::find($id);
        $item = ProductItem::where('product_group_id', $id)->get();

        // $org_id = RegularMerchantController::getOrganizationId();
        $org_code = Organization::find($group->organization_id)->code;
        
        $image_url = "/merchant-image/product-item/".$org_code."/";
        
        return view('merchant.regular.admin.product.show', compact('item', 'group', 'image_url'));
    }

    public function getAllProductItem(Request $request)
    {
        $g_id = $request->id;

        $item = ProductItem::where('product_group_id', $g_id)->get();

        if(request()->ajax()) 
        { 
            $table = Datatables::of($item);

            $table->editColumn('desc', function ($row) {
                if($row->desc != null) { $desc = $row->desc; }
                else { $desc = 'Tiada Deskripsi'; }
                
                return $desc;
            });

            $table->editColumn('image', function ($row) {
                $group = ProductGroup::find($row->product_group_id);
                $org_code = Organization::find($group->organization_id)->code;
                $image_url = "/merchant-image/product-item/".$org_code."/";

                if($row->image != null){
                    $img = '<img class="rounded img-fluid bg-dark" id="img-size" src="'.$image_url.$row->image.'">';
                } else {
                    $img = '<img class="rounded img-fluid bg-dark" id="img-size" src="'.url('images/koperasi/default-item.png').'">';
                }
                return $img;
            });

            $table->editColumn('inventory', function ($row) {
                $type = $row->type == 'have inventory' ? $row->quantitiy_available : 'Tiada Inventori';
                
                $inv = '<ul class="list-group"><li class="list-group-item d-flex justify-content-between align-items-center">';
                $inv .= 'Inventori<span class="badge badge-primary badge-pill">'.$type;
                $inv .= '</span></li><li class="list-group-item d-flex justify-content-between align-items-center">';
                $inv .= 'Kuantiti Dijual<span class="badge badge-primary badge-pill">'.$row->selling_quantity;
                $inv .= '</span></li><li class="list-group-item d-flex justify-content-between align-items-center">';
                $inv .= 'Kata Nama Kuantiti<span class="badge badge-primary badge-pill">'.$row->collective_noun;
                $inv .= '</span></li></ul>';
                return $inv;
            });

            $table->editColumn('price', function ($row) {
                $price_unit = number_format($row->price, 2);
                $overall_price = number_format($row->price * $row->selling_quantity, 2);

                $price = '<ul class="list-group"><li class="list-group-item d-flex justify-content-between align-items-center">';
                $price .= 'Seunit<span class="badge badge-primary badge-pill">'.$price_unit;
                $price .= '</span></li><li class="list-group-item d-flex justify-content-between align-items-center">';
                $price .= 'Setiap Kuantiti<span class="badge badge-primary badge-pill">'.$overall_price;
                $price .= '</span></li></ul>';
                return $price;
            });

            $table->editColumn('status', function ($row) {
                if ($row->status == 1) {
                    $label = "<span class='badge rounded-pill bg-success text-white p-2'>Aktif</span>";
                    return $label;
                } else {
                    $label = "<span class='badge rounded-pill bg-danger text-white p-2'>Tidak Aktif</span>";
                    return $label;
                }
            });

            $table->editColumn('action', function ($row) {
                $org_code = DB::table('product_group as pg')
                ->join('organizations as o', 'pg.organization_id', 'o.id')
                ->where('pg.id', $row->product_group_id)
                ->select('o.code')
                ->first()->code;
                $image_url = "/merchant-image/product-item/".$org_code."/";

                $btn = '<a href="'.route('admin-reg.edit-item', ['id' => $row->product_group_id, 'item' => $row->id]).'" class="edit-item-modal btn btn-primary m-1"><i class="fas fa-pencil-alt"></i></a>';
                $btn .= '<button data-item-id="'.$row->id.'" data-image-url="'.$image_url.'" class="delete-item-modal btn btn-danger m-1"><i class="fas fa-trash-alt"></i></button>';
                return $btn;
            });

            $table->rawColumns(['image', 'inventory', 'price', 'status', 'action']);

            return $table->make(true);
        }
    }

    public function updateProductGroup(Request $request)
    {
        $group = ProductGroup::where('id', $request->group_id)->update([
            'name' => $request->name,
        ]);
        
        if($group) {
            return back()->with('success', 'Jenis Produk Berjaya Dikemaskini');
        } else {
            return back()->with('error', 'Error. Tidak berjaya');
        }
    }

    public function destroyProductGroup(Request $request)
    {
        $item = DB::table('product_item')
        ->where('product_group_id', $request->group_id)->get();

        foreach($item as $row)
        {
            if($row->image != NULL)
            {
                $file = public_path($request->image_url.$row->image);
                $exists = File::exists($file);
                
                if($exists)
                {
                    File::delete($file);
                }
            }

            DB::table('product_item')->where('id',$row->id)->update([
                'status' => 0,
                'deleted_at' => Carbon::now()->toDateTimeString(),
            ]);
        }

        $delete_group = ProductGroup::find($request->group_id)->delete();

        if($delete_group) {
            return redirect('/admin-regular/p-group-list')->with('success', 'Jenis Produk Berjaya Dibuang');
        } else {
            return back()->with('error', 'Error. Tidak berjaya');
        }
    }

    public function storeProductItem(Request $request)
    {
        if($this->checkProductItemSameName($request->org_id, $request->item_name)) {
            return back()->with('error', 'Item sudah wujud dalam kumpulan ini');
        }
        
        $file_name = $this->storeProductItemImage($request->org_id, $request->item_image);
        
        $item = ProductItem::create([
            'name' => $request->item_name,
            'desc' => $request->item_desc,
            'type' => $request->inventory,
            'price' => $request->item_price,
            'selling_quantity' => $request->selling_quantity,
            'collective_noun' => $request->collective_noun,
            'image' => $file_name,
            'status' => $request->status,
            'product_group_id' => $request->group_id,
        ]);
            
        if($request->inventory == "have inventory") {
            ProductItem::where('id', $item->id)->update([
                'quantity_available' => $request->item_quantity,
            ]);
        }

        return back()->with('success', 'Item Baru Berjaya Direkodkan');
    }

    private function checkProductItemSameName($org_id, $item_name)
    {
        $isSame = false;
        $item = DB::table('product_item as pi')->join('product_group as pg', 'pg.id', 'pi.product_group_id')
        ->where('pg.organization_id', $org_id)->where('pi.deleted_at', null)->select('pi.name as name')->get();
        
        foreach($item as $row)
        {
            $isSame = (strtolower($row->name) == strtolower($item_name)) ? true : false;
        }

        return $isSame;
    }

    private function storeProductItemImage($org_id, $item_image)
    {
        $date = implode('_', explode('-',Carbon::now()->toDateString()));
        $time = implode('', explode(':',Carbon::now()->toTimeString()));
        
        $str = $date.'_'.$time.'_'.rand();
        
        $file_name = NULL;
        
        if (!is_null($item_image)) {
            $org_code = Organization::find($org_id)->code;
            
            $extension = $item_image->extension();
            $storagePath  = $item_image->move(public_path('merchant-image/product-item/'.$org_code), $str.'.'.$extension);
            $file_name = basename($storagePath);
        }

        return $file_name;
    }

    public function editProductItem(Request $request, $g_id, $i_id)
    {

        $item = ProductItem::find($i_id);
        $group = ProductGroup::find($g_id);
        $org_code = Organization::find($group->organization_id)->code;

        $image_url = "merchant-image/product-item/".$org_code."/";

        return view('merchant.regular.admin.product.edit', compact('item', 'image_url', 'group'));
    }

    public function updateProductItem(Request $request)
    {        
        $alert = $this->validateUpdateProductItem($request);

        if(!empty($alert)) {
            return back()->with('error', $alert);
        }
        
        $item_old_image = ProductItem::find($request->id)->image;

        $image_arr = array(
            'img' => $request->item_image,
            'img_url' => $request->image_url,
            'existing_img' => $item_old_image
        );
        
        $file_name = $this->updateProductItemImage($image_arr);

        ProductItem::where('id', $request->id)->update([
            'name' => $request->item_name,
            'desc' => $request->item_desc,
            'type' => $request->inventory,
            'price' => number_format($request->item_price, 2, '.', ''),
            'selling_quantity' => $request->selling_quantity,
            'collective_noun' => $request->collective_noun,
            'image' => $file_name,
            'status' => $request->status,
        ]);

        if($request->inventory == "have inventory") {
            ProductItem::where('id', $request->id)->update([
                'quantity_available' => $request->item_quantity,
            ]);
        } elseif($request->inventory == "no inventory") {
            ProductItem::where('id', $request->id)->update([
                'quantity_available' => null
            ]);
        }
        
        return back()->with('success', 'Berjaya dikemaskini');
    }

    public function updateProductItemImage($image_arr)
    {
        $file_name = NULL;

        // If item image exists
        if(!is_null($image_arr['existing_img']))
        {
            $file_name = $image_arr['existing_img'];
        }
        
        // If the admin want to change the image
        if (!is_null($image_arr['img'])) {
            $date = implode('_', explode('-',Carbon::now()->toDateString()));
            $time = implode('', explode(':',Carbon::now()->toTimeString()));
            
            $str = $date.'_'.$time.'_'.rand();
            
            // get existing image
            $file = public_path($image_arr['img_url'].$image_arr['existing_img']);

            // if the existing image is exist then delete
            if(File::exists($file))
            {
                File::delete($file);
            }
            
            // store new image
            $extension = $image_arr['img']->extension();
            $storagePath  = $image_arr['img']->move(public_path($image_arr['img_url']), $str.'.'.$extension);
            $file_name = basename($storagePath);
        }

        return $file_name;
    }

    private function validateUpdateProductItem($request)
    {
        $alert = "";
        $isSame = false;
        $item = DB::table('product_item as pi')->join('product_group as pg', 'pg.id', 'pi.product_group_id')
        ->where('pg.organization_id', $request->org_id)->select('pi.name as name')->get();

        $old_name = DB::table('product_item')->where('id', $request->id)->select('name')->first()->name;

        foreach($item as $row)
        {
            $isSame = (strtolower($row->name) == strtolower($request->item_name)) ? true : false;
        }

        if(($request->inventory == "no inventory" && empty($request->item_name) && empty($request->item_price) && empty($request->selling_quantity) && empty($request->collective_noun)) ||
         ($request->inventory == "have inventory" && empty($request->item_name) && empty($request->item_price) && empty($request->item_quantity) && empty($request->selling_quantity) && empty($request->collective_noun)))
        {
            $alert .= " Sila isi tempat kosong yang diperlukan.";
        }

        if($isSame && $request->item_name != $old_name) {
            $alert .= " Terdapat item dengan nama yang sama dalam organisasi anda";
        }

        return $alert;
    }

    public function displayDestroyItemBody(Request $request)
    {
        $item_name = ProductItem::find($request->i_id)->name;
        $body = "Adakah anda pasti mahu buang <strong>".$item_name."</strong>?";
        return response()->json(['body' => $body]);
    }

    public function destroyProductItem(Request $request)
    {
        $item = DB::table('product_item')->where('id', $request->i_id);

        $item_image = $item->first()->image;
        
        if($item_image != NULL)
        {
            $file = public_path($request->image_url.$item_image);
            $exists = File::exists($file);
            
            if($exists)
            {
                File::delete($file);
            }
        }
        
        $item->update(['status' => 0, 'deleted_at' => Carbon::now()->toDateTimeString()]);

        Session::flash('success', 'Item Berjaya Dibuang');
    }
}
