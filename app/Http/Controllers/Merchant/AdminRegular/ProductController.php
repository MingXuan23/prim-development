<?php

namespace App\Http\Controllers\Merchant\AdminRegular;

use App\Models\Organization;
use App\Models\TypeOrganization;
use App\Models\ProductItem;
use App\Models\ProductGroup;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;

class ProductController extends Controller
{
    private function getOrganizationId()
    {
        $role_id = DB::table('organization_roles')->where('nama', 'Regular Merchant Admin')->first()->id;
        $type_org_id = TypeOrganization::where('nama', 'Peniaga Barang Umum')->first()->id;

        $org_id = DB::table('organizations as o')
        ->join('organization_user as ou', 'ou.organization_id', 'o.id')
        ->where([
            ['user_id', Auth::id()],
            ['role_id', $role_id],
            ['status', 1],
            ['type_org', $type_org_id],
            ['deleted_at', NULL],
        ])
        ->select('o.id')
        ->first()->id;
        
        return $org_id;
    }

    public function indexProductGroup()
    {
        $org_id = $this->getOrganizationId();

        $group = ProductGroup::where('organization_id', $org_id)->get();

        return view('merchant.regular.admin.product.index', compact('group'));
    }

    public function storeProductGroup(Request $request)
    {
        $org_id = $this->getOrganizationId();
        # Insert Product Group Data
        $pg = ProductGroup::create([
            'name' => $request->name,
            'organization_id' => $org_id,
        ]);

        if($pg) {
            return back()->with('success', 'Jenis Produk Berjaya Direkodkan');
        } else {
            return back()->with('error', 'Error. Tidak berjaya');
        }
    }

    public function showProductItem($id)
    {
        $url_name = array();
        $group = ProductGroup::find($id);
        $item = ProductItem::where('product_group_id', $id)->get();

        $org_id = $this->getOrganizationId();
        $org_name = Organization::find($org_id)->nama;

        foreach($item as $row){
            $link = explode(" ", $row->name);
            $str = implode("-", $link);
            $url_name[$row->id] = $str;
        }
        
        $image_url = "merchant-image/product-item/".$org_name."/";
        
        return view('merchant.regular.admin.product.show', compact('item', 'group', 'image_url', 'url_name'));
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
            return redirect('/merchant/admin-regular/product-group-list')->with('success', 'Jenis Produk Berjaya Dibuang');
        } else {
            return back()->with('error', 'Error. Tidak berjaya');
        }
    }

    public function storeProductItem(Request $request)
    {
        if($this->checkProductItemSameName($request->org_id, $request->item_name)) {
            return back()->with('error', 'Item sudah wujud dalam kumpulan ini');
        }
        
        $file_name = $this->storeProductItemImage($request->item_name, $request->org_id, $request->item_image);
        
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
        ->where('pg.organization_id', $org_id)->select('pi.name as name')->get();
        
        foreach($item as $row)
        {
            $isSame = (strtolower($row->name) == strtolower($item_name)) ? true : false;
        }

        return $isSame;
    }

    private function storeProductItemImage($item_name, $org_id, $item_image)
    {
        $link = explode(" ", $item_name);
        $str = implode("-", $link);
        $file_name = NULL;

        $org_name = DB::table('organizations')->where('id', $org_id)->first()->nama;
        
        if (!is_null($item_image)) {
            $extension = $item_image->extension();
            $storagePath  = $item_image->move(public_path('merchant-image/product-item/'.$org_name), $str.'.'.$extension);
            $file_name = basename($storagePath);
        }

        return $file_name;
    }

    public function editProductItem(Request $request, $id, $item_url)
    {
        $link = explode("-", $item_url);
        $item_name = implode(" ", $link);

        $item = ProductItem::where('product_group_id', $id)->where('name', $item_name)->first();
        $group = ProductGroup::find($id);
        $org_name = Organization::find($group->organization_id)->nama;

        $image_url = "merchant-image/product-item/".$org_name."/";

        return view('merchant.regular.admin.product.edit', compact('item', 'image_url', 'group'));
    }

    public function updateProductItem(Request $request)
    {        
        $alert = $this->validateUpdateProductItem($request);

        if(!empty($alert)) {
            return back()->with('error', $alert);
        }
        
        $image_arr = array(
            'id' => $request->id,
            'name' => $request->item_name,
            'img' => $request->item_image,
            'img_url' => $request->image_url,
        );

        $item_old_image = ProductItem::find($request->id)->image;
        
        $file_name = $this->updateProductItemImage($image_arr, $item_old_image);

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

    public function updateProductItemImage($image_arr, $existing_image)
    {
        $file_name = NULL;

        // If item image exists
        if(!is_null($existing_image))
        {
            $file_name = $existing_image;
        }
        
        // If the admin want to change the image
        if (!is_null($image_arr['img'])) {
            $link = explode(" ", $image_arr['img']->getClientOriginalName().'='.$image_arr['name']);
            $str = implode("-", $link);
            // get existing image
            $file = public_path($image_arr['img_url'].$existing_image);

            // if the existing image is exist then delete
            if(File::exists($file))
            {
                File::delete($file);
                // Storage::disk('public')->delete($image_arr['img_url'].$existing_image);
            }
            
            // store new image
            $extension = $image_arr['img']->extension();
            $storagePath  = $image_arr['img']->move(public_path($image_arr['img_url']), $str.'.'.$extension);
            $file_name = basename($storagePath);

            Artisan::call('cache:clear');
        }

        return $file_name;
    }

    private function validateUpdateProductItem($request)
    {
        $alert = "";
        $isSame = false;
        $item = DB::table('product_item as pi')->join('product_group as pg', 'pg.id', 'pi.product_group_id')
        ->where('pg.organization_id', $request->org_id)->select('pi.name as name')->get();

        foreach($item as $row)
        {
            $isSame = (strtolower($row->name) == strtolower($request->item_name)) ? true : false;
        }

        if(($request->inventory == "no inventory" && empty($request->item_name) && empty($request->item_price) && empty($request->selling_quantity) && empty($request->collective_noun)) ||
         ($request->inventory == "have inventory" && empty($request->item_name) && empty($request->item_price) && empty($request->item_quantity) && empty($request->selling_quantity) && empty($request->collective_noun)))
        {
            $alert .= " Sila isi tempat kosong yang diperlukan.";
        }

        if($isSame) {
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
