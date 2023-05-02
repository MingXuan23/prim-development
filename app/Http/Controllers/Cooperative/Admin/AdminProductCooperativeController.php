<?php

namespace App\Http\Controllers\Cooperative\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ProductItem;
use Yajra\DataTables\DataTables;
use App\Models\Organization;

use Carbon\Carbon;
use App\Models\Fee;
use App\Models\Fee_New;
use App\Models\Category;
use App\Models\ClassModel;
use Psy\Command\WhereamiCommand;

use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\AppBaseController;
use Symfony\Component\VarDumper\Cloner\Data;


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
       
        if (request()->ajax()){
            $userID=Auth::id();
            $product = DB::table('product_item as p')
            ->join('product_group as pg', 'pg.id', '=', 'p.product_group_id')
            ->join('organization_user as ou','pg.organization_id','=','ou.organization_id')
            ->select('p.*','pg.name as type_name')
            ->where('ou.user_id', $userID)
            ->whereNull('p.deleted_at')
            ->distinct('ou.user_id')
            ->get();
            
        $table = Datatables::of($product);
                $table->addColumn('desctext', function ($row) {
                    $target=json_decode($row->target);
                    $desctext='<div class="d-flex" >';//add tag to the description string

                    //if the target is array 
                    if(is_array($target->data)){
                    $desctext=$desctext. '<span style="text-align: left;">Kepada Tahun '.$target->data[0];
                    //add text
                        for($i=1;$i<count($target->data);$i++)
                        {
                            $desctext=$desctext. ',Tahun '.$target->data[$i];
                            //add other tahun if exist
                        }
                    }//else the target is not array,the target is to all tahun
                    else{
                        $desctext=$desctext. '<span style="text-align: left;">Kepada Tahun Semua. ';
                    }
                    $desctext=$desctext.'<br>'.$row->desc.'</span></div>';
                    //add description to the string

                    
                    return $desctext;
                });

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
                    $btn = $btn . '<a href="' . route('koperasi.editProduct', $row->id) . '" class="btn btn-primary m-1">Edit</a>';
                    //$btn = $btn . '<button id="' . $row->id . '" data-token="' . $token . '" class="btn btn-danger m-1">Buang</button></div>';
                    return $btn;
                });

                $table->rawColumns(['status', 'action','desctext']);
                
                return $table->make(true);
        }
    }

    public function getProductNumOfGroup(Request $request){
        
        $userID = Auth::id();
        
        $productNum = DB::table('product_item as p')
        ->join('product_group as pg', 'pg.id', '=', 'p.product_group_id')
        ->join('organization_user as ou','pg.organization_id','=','ou.organization_id')
        ->select('count(p.id) as productCount')
        ->where('ou.user_id', $userID)
        ->where('pg.id',$request->groupId)
        ->distinct('ou.user.id')
        ->first();

        return response()->json(['status' => 'success','productNum' => $productNum]);
        
    }

    public function deleteType(Int $id)
    {
        $delete = DB::table('product_group')->where('id',$id)->update([
            'deleted_at' => now(),   
        ]);
        $removeType = DB::table('product_item')->where('id',$id)->update([
            'deleted_at'=>now(),
            'status'=>0,
        ]);
        return redirect('koperasi/produktype')->with('success','Produk type berjaya dipadam');
    }

    public function editType(Int $id)
    {
        $edit = DB::table('product_group')->where('id',$id)->first();

        return view('koperasi-admin.editType')->with('edit',$edit);
    }

    public function updateType(Int $id,Request $request)
    {    
      
        $update = DB::table('product_group')->where('id',$id)->update([
            'name' => $request->name,  
            'updated_at'=>now()        
        ]);
        return redirect('koperasi/produktype')->with('success','Produk type berjaya diubah');
    }

    public function createType()
    {
        $userID = Auth::id();
        $org = DB::table('organizations as o')
        ->join('organization_user as os', 'o.id', 'os.organization_id')
        ->where('os.user_id', $userID)
        ->where('o.type_org',10)
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
                ->where('o.type_org',10)
                ->select('o.id')
                ->first();

       $add = DB::table('product_group') -> insert([
        'name' => $request->input('name'), 
        'organization_id' =>$org->id,
        'created_at'=>now(),
        'updated_at'=>now(),
       ]);

       return redirect('koperasi/produktype')->with('success','Produk type berjaya ditambah.');
    }

    public function createProduct()
    {
        $userID = Auth::id();
        $org = DB::table('organizations as o')
        ->join('organization_user as os', 'o.id', 'os.organization_id')
        ->where('os.user_id', $userID)
        ->where('o.type_org',10)
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
        if($request->cb_year){
            $data = array(
                'data' =>$request->cb_year
            );
            
        }
        else{
            $data=['data' => 'All'];
        }
        $target = json_encode($data);
       $add = DB::table('product_item') -> insert([
        'name' => $request->input('nama'),
        'desc' => $request->input('description'),
        'image' => $file_name,
        'quantity_available' => $request->input('quantity'),
        'price' => $request->input('price'),
        'status'=> $request->input('status'),
        'target'=>$target,
        'created_at' => now(),
        'updated_at' => now(),
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
        ->where('o.type_org',10)
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
        // $org = DB::table('organizations as o')
        // ->join('organization_user as os', 'o.id', 'os.organization_id')
        // ->where('os.user_id', $userID)
        // ->where('o.type_org',10)
        // ->select('o.id')
        // ->first();
      
        $update = DB::table('product_item')->where('id',$id)->update([
            'name' => $request->nama,
            'desc' => $request->description,
            'image' => $request->image,
            'quantity_available' => $request->quantity,
            'price' => $request->price,
            'status'=> $request->status,
            'product_group_id' => $request->type,
            'updated_at' => now()
        ]);
        if($request->quantity ==0)
        {
            DB::table('product_item')->where('id',$id)->update(['status'=> 0]);
        }
        return redirect('koperasi/produkmenu')->with('success','Product updated successfully.');
    }


    public function deleteProduct($id)
    {
        $delete = DB::table('product_item')->where('id',$id)->update([
            'deleted_at' => now(),   
            'status'=>0     
        ]);
        return redirect('koperasi/produkmenu')->with('success','Product deleted successfully.');
    }

    public function deleteSelectedProducts(Request $request)
    {
        $itemArray = $request->input('itemArray');
        foreach($itemArray as $id)
        {
            $delete = DB::table('product_item')->where('id',$id)->update([
                'deleted_at' => now(),   
                'status'=>0     
            ]);
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
            case 3:
                $returnInformation="Cancel adding a product type.";
                break;
            case 4:
                $returnInformation="Cancel editing a product type.";
                break;

        }
        return redirect('koperasi/produkmenu')->with('success',$returnInformation);
    }

    public function fetchClassyear(){
        
        
        $userID = Auth::id();
        $oid = DB::table('organizations as o')
                ->join('organization_user as os', 'o.id', 'os.organization_id')
                ->where('os.user_id', $userID)
                ->where('o.type_org',10)
                ->select('o.parent_org')
                ->first();
        
        $organization = Organization::find($oid->parent_org);

        if ($organization->parent_org != null)
        {
            $oid = $organization->parent_org;
        }
        

        $list = DB::table('organizations')
        ->select('organizations.id as oid', 'organizations.nama as organizationname', 'organizations.type_org')
        ->where('organizations.id', $oid->parent_org)
        ->first();

        $class_organization = DB::table('classes')
            ->join('class_organization', 'class_organization.class_id', '=', 'classes.id')
            ->select(DB::raw('substr(classes.nama, 1, 1) as year'))
            ->distinct()
            ->where('classes.status', 1)
            ->where('class_organization.organization_id', $oid->parent_org)
            ->get();

        $class=DB::table('classes')
        ->join('class_organization', 'class_organization.class_id', '=', 'classes.id')
        ->select('classes.id','classes.nama')
        ->distinct()
        ->where('classes.status', 1)
        ->where('class_organization.organization_id', $oid->parent_org)
        ->get();

         //dd($class_organization);
         
        return response()->json(['data' => $list, 'datayear' => $class_organization,'classes'=>$class]);
        
    }
}
