<?php

namespace App\Http\Controllers\Cooperative\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ProductItem;
use Yajra\DataTables\DataTables;


class AdminProductCooperativeController extends Controller
{
    public function indexAdmin( )
    {
        $role_id = DB::table('roles')->where('name', 'Koop Admin')->first()->id;
        $userID = Auth::id();
        $koperasi = DB::table('organizations as o')
                    ->join('organization_user as ou', 'o.id', '=', 'ou.organization_id')
                    ->where('ou.user_id', $userID)
                    ->where('ou.role_id', $role_id)
                    ->first();

        $product = DB::table('product_item as p')
                    ->join('product_group as pg', 'pg.id', '=', 'p.product_group_id')
                    ->join('organization_user as ou','pg.organization_id','=','ou.organization_id')
                    ->select('p.*','pg.name as type_name')
                    ->where('ou.user_id', $userID)
                    ->distinct('ou.user.id')
                    ->get();
        return view('koperasi-admin.index', compact('koperasi'))
        ->with('product',$product);
    }
    
    public function productMenu()
    {
        $role_id = DB::table('roles')->where('name','Koop Admin')->first()->id;
        $userID = Auth::id();
        $koperasi = DB::table('organizations as o')
        ->join('organization_user as ou', 'o.id', '=', 'ou.organization_id')
        ->where('ou.user_id', $userID)
        ->where('ou.role_id', $role_id)
        ->first();

        $product = DB::table('product_item as p')
        ->join('product_group as pg', 'pg.id', '=', 'p.product_group_id')
        ->join('organization_user as ou','pg.organization_id','=','ou.organization_id')
        ->select('p.*','pg.name as type_name')
        ->where('ou.user_id', $userID)
        ->distinct('ou.user.id')
        ->get();

        $group = DB::table('product_group as pg')
        ->join('organization_user as ou', 'pg.organization_id', '=', 'ou.organization_id')
        ->where('ou.user_id', $userID)
        ->select('pg.*')
        ->distinct('pg.id')
        ->get();
        //dd($group,$koperasi);
        
        return view('koperasi-admin.productmenu', compact('koperasi'),compact('group','product'));
    }

    public function getProductList(Request $request){
       dd($request);
        if (request()->ajax()){
            $userID=Auth::id();
            $product = DB::table('product_item as p')
            ->join('product_group as pg', 'pg.id', '=', 'p.product_group_id')
            ->join('organization_user as ou','pg.organization_id','=','ou.organization_id')
            ->select('p.*','pg.name as type_name')
            ->where('ou.user_id', $userID)
            ->distinct('ou.user_id')
            ->get();
            
        $table = Datatables::of($product);

                $table->addColumn('status', function ($row) {
                    if ($row->status == '1') {
                        $btn = '<div class="d-flex justify-content-center">';
                        $btn = $btn . '<span class="badge badge-success">Aktif</span></div>';

                        return $btn;
                    } else {
                        $btn = '<div class="d-flex justify-content-center">';
                        $btn = $btn . '<span class="badge badge-danger"> Tidak Aktif </span></div>';

                        return $btn;
                    }
                });

                $table->addColumn('action', function ($row) {
                    $token = csrf_token();
                    $btn = '<div class="d-flex justify-content-center">';
                    $btn = $btn . '<a href="' . route('student.edit', $row->id) . '" class="btn btn-primary m-1">Edit</a>';
                    //$btn = $btn . '<button id="' . $row->id . '" data-token="' . $token . '" class="btn btn-danger m-1">Buang</button></div>';
                    return $btn;
                });

                $table->rawColumns(['status', 'action']);
                
                return $table->make(true);
        }
    }

    public function deleteType(Int $id)
    {
        $delete = DB::table('product_group')->where('id',$id)->delete();
        return redirect('koperasi/produktype')->with('success','Produk type berjaya dipadam');
    }

    public function editType(Int $id)
    {
        $edit = DB::table('product_group')->where('id',$id)->first();

        return view('koperasi.editType')->with('edit',$edit);
    }

    public function updateType(Int $id)
    {    

        $update = DB::table('product_group')->where('id',$id)->update([
            'name' => $request->nama          
        ]);
        return redirect('koperasi/produktype')->with('success','Produk type berjaya diubah');
    }

    public function createType()
    {
        $userID = Auth::id();
        $org = DB::table('organizations as o')
        ->join('organization_user as os', 'o.id', 'os.organization_id')
        ->where('os.user_id', $userID)
        ->select('o.id')
        ->first();

        /*
        $type = DB::table('product_group as p')
        ->where('p.organization_id',$org->id)
        ->get();*/ //mingxuan :not sure what is it but i just comment it

        $group = DB::table('product_group as pg')
        ->join('organization_user as ou', 'pg.organization_id', '=', 'ou.organization_id')
        ->where('ou.user_id', $userID)
        ->where ('ou.organization_id',$org->id)
        ->select('pg.*')
        ->distinct('pg.id')
        ->get();

        return view('koperasi-admin.addtype',compact('group'));
    }

    public function storeType(Request $request)
    {
        $userID = Auth::id();
        $org = DB::table('organizations as o')
                ->join('organization_user as os', 'o.id', 'os.organization_id')
                ->where('os.user_id', $userID)
                ->select('o.id')
                ->first();

       $add = DB::table('product_group') -> insert([
        'name' => $request->input('name'), 
        'organization_id' =>$org->id,
       ]);

       return redirect('koperasi/produktype')->with('success','Produk type berjaya ditambah.');
    }

    public function createProduct()
    {
        $userID = Auth::id();
        $org = DB::table('organizations as o')
        ->join('organization_user as os', 'o.id', 'os.organization_id')
        ->where('os.user_id', $userID)
        ->select('o.id')
        ->first();

        $type = DB::table('product_group as p')
        ->where('p.organization_id',$org->id)
        ->get();
        if(count($type)==0)
        {
            return redirect('koperasi/produkmenu')->with('success','Sila tambah produk type dahulu'); //use success session only
        }
        return view('koperasi-admin.add',compact('type'));
    }

    public function storeProduct(Request $request)
    {
        $link = explode(" ", $request->nama);
        $str = implode("-", $link);
        // dd($request->organization_picture);
        
        $file_name = '';

        if (!is_null($request->image)) {
            $extension = $request->image->extension();
            $storagePath  = $request->image->move(public_path('koperasi-item'), $str . '.' . $extension);
            $file_name = basename($storagePath);
        }
        else
        {
            $file_name = null;
        }

        $userID = Auth::id();
        $org = DB::table('organizations as o')
                ->join('organization_user as os', 'o.id', 'os.organization_id')
                ->where('os.user_id', $userID)
                ->select('o.id')
                ->first();


       $add = DB::table('product_item') -> insert([
        'name' => $request->input('nama'),
        'desc' => $request->input('description'),
        'image' => $file_name,
        'quantity_available' => $request->input('quantity'),
        'price' => $request->input('price'),
        'status'=> $request->input('status'),
        'product_group_id' => $request->input('type'),   
       ]);


    //    $add = DB::table('product_item');
    //    if($request->hasfile('image'))
    //    {
    //        $request -> file('image')->move('photo/',$request->file('image')->getClientOriginalName());
    //        $add->image =$request->file('image')->getClientOriginalName();
    //        $add -> upsert(['image' => $request->input('image')]);
    //    }
       return redirect('koperasi/produkmenu')->with('success','Product created successfully.');
    }

    public function editProduct(Int $id)
    {    
        $userID = Auth::id();

        $org = DB::table('organizations as o')
        ->join('organization_user as os', 'o.id', 'os.organization_id')
        ->where('os.user_id', $userID)
        ->select('o.id')
        ->first();

  

        $edit = DB::table('product_item')->where('id',$id)->first();

        $test = DB::table('product_item as p')
        ->join('product_group as pt', 'p.product_group_id', '=', 'pt.id')
        ->select('p.*', 'pt.name as type_name')
        ->get()
        ->where('id',$id)
        ->first();

        $type = DB::table('product_group as p')
                ->where('p.organization_id',$org->id)
                ->get();
  
        return view('koperasi-admin.edit',compact('type'),compact('test'))
        ->with('test',$test)
        ->with('edit',$edit);
    }

    public function updateProduct(Request $request,Int $id)
    {
        $userID = Auth::id();
        $org = DB::table('organizations as o')
        ->join('organization_user as os', 'o.id', 'os.organization_id')
        ->where('os.user_id', $userID)
        ->select('o.id')
        ->first();
      
        $update = DB::table('product_item')->where('id',$id)->update([
            'name' => $request->nama,
            'desc' => $request->description,
            'image' => $request->image,
            'quantity_available' => $request->quantity,
            'price' => $request->price,
            'status'=> $request->status,
            'product_group_id' => $request->type,
            
          
        ]);
        if($request->quantity ==0)
        {
            DB::table('product_item')->where('id',$id)->update(['status'=> 0]);
        }
        return redirect('koperasi/produkmenu')->with('success','Product updated successfully.');
    }


    public function deleteProduct($id)
    {

        $delete = DB::table('product_item')->where('id',$id)->delete();
        return redirect('koperasi/produkmenu')->with('success','Product deleted successfully.');
    }

    public function deleteSelectedProducts(Request $request)
    {
        $itemArray = $request->input('itemArray');
        foreach($itemArray as $id)
        {
            $delete = DB::table('product_item')->where('id',$id)->delete();
        }
    // perform any necessary operations on $itemArray

    return response()->json(['status' => 'success']);
        

    }

    public function returnProdukMenu($page)
    {
        $returnInformation="";
        switch($page)
        {
            case 1:
                $returnInformation="Cancel creating a product.";
                break;
            case 2:
                $returnInformation="Cancel editing a product.";
                break;
        }
        return redirect('koperasi/produkmenu')->with('success',$returnInformation);
    }
}
