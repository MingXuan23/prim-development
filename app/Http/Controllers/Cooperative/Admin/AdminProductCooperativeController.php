<?php

namespace App\Http\Controllers\Cooperative\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\ProductItem;
use Yajra\DataTables\DataTables;
use App\Models\Organization;
use App\Imports\ProductTypeImport;
use App\Imports\ProductImport;

use Carbon\Carbon;
use App\Models\Fee;
use App\Models\Fee_New;
use App\Models\Category;
use App\Models\Transaction;
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
    
    //first time to fetch the product menu
    public function productMenu(Request $request)
    {
        //$this->testing();
        $role_id = DB::table('roles')->where('name','Koop Admin')->first()->id;
        $userID = Auth::id();

        $koperasiList = DB::table('organizations as o')
        ->join('organization_user as ou', 'o.id', '=', 'ou.organization_id')
        ->where('ou.user_id', $userID)
        ->where('ou.role_id', $role_id)
        ->get();
        //d($request);
        $koperasi = $koperasiList->first();
        $koopId=$request->session()->get('koopId');
        if($koopId!=null)
            $koperasi=$koperasiList->where('organization_id',$koopId)->first();
        //dd($koperasi->organization_id,$koopId,$request);
        $getResult=$this->getProductMenuByOrgId($koperasi->organization_id);
        $group =$getResult['group'];
        $product=$getResult['product'];
        $hour=$getResult['hour'];
       
        $reminderMessage="";
        if(count($hour)==0)
        {
            $reminderMessage="Anda belum kemas kini waktu operasi koperasi anda. Sila pergi 'Hari Dibuka' dekat 'Koop Admin' untuk mengemaskini maklumat.";
        }
        //dd($koperasi->organization_id);
        return view('koperasi-admin.productmenu', compact('koperasi','koperasiList'),compact('group','product','reminderMessage'));
    }

    //get the information throught the organization id
    public function getProductMenuByOrgId($koopId){
        
        $product = DB::table('product_item as p')
        ->join('product_group as pg', 'pg.id', '=', 'p.product_group_id')
        ->join('organization_user as ou','pg.organization_id','=','ou.organization_id')
        ->select('p.*','pg.name as type_name')
        ->where('ou.organization_id', $koopId)
        ->whereNull('p.deleted_at')
        ->distinct('ou.user.id')
        ->get();

        $group = DB::table('product_group as pg')
        ->join('organization_user as ou', 'pg.organization_id', '=', 'ou.organization_id')
        ->where('ou.organization_id', $koopId)
        ->where('pg.organization_id',$koopId)
        ->whereNull('pg.deleted_at')
        ->select('pg.*')
        ->distinct('pg.id')
        ->get();
        
        $hour = DB::table('organization_hours as oh')
                ->join('organization_user as ou','oh.organization_id','=','ou.organization_id')
                ->join('organizations as o', 'o.id', '=', 'ou.organization_id')
                ->select('oh.*')
                ->distinct('o.id')
                //->where('ou.user_id', $userID)
                ->where('o.type_org',10)
                ->where('o.id',$koopId)
                ->where('oh.status',1)
                ->get();
        $reminderMessage="";
        if(count($hour)==0)
        {
            $reminderMessage="Anda belum kemas kini waktu operasi koperasi anda. Sila pergi 'Hari Dibuka' dekat 'Koop Admin' untuk mengemaskini maklumat.";
        }
        $data = [
            'group'=>$group,'product'=>$product,'reminderMessage'=> $reminderMessage,'hour'=>$hour
        ];
    
        // Return the array
        return $data;
        
    }

    public function getProductList(Request $request){ //ajax request to show product in product menu
       
        if (request()->ajax()){

            $role_id = DB::table('roles')->where('name','Koop Admin')->first()->id;
            $userID = Auth::id();
            $koopId=$request->koopId;
        
            $product = DB::table('product_item as p')
            ->join('product_group as pg', 'pg.id', '=', 'p.product_group_id')
            ->join('organization_user as ou','pg.organization_id','=','ou.organization_id')
            ->select('p.*','pg.name as type_name')
            ->where('ou.user_id', $userID)
            ->where('pg.organization_id',$koopId)
            ->whereNull('p.deleted_at')
            ->distinct('ou.user_id')
            ->get();
            
        $table = Datatables::of($product);
                $table->addColumn('desctext', function ($row) {
                    $target=json_decode($row->target);
                    $desctext='<div class="d-flex" >';//add tag to the description string
                    //if the target is array 
                    if($target!=null && is_array($target->data)){
                        if (strpos($target->data[0], "T") !== false){
                            $desctext=$desctext. '<span style="text-align: left;">Kepada Tahun '.$target->data[0][1];
                            for($i=1;$i<count($target->data);$i++)
                            {
                                $desctext=$desctext. ',Tahun '.$target->data[$i][1];
                                //add other tahun if exist
                            }
                        }else{
                            $desctext=$desctext. '<span style="text-align: left;">Kepada Kelas '.$this->getClassNameById($target->data[0]);

                            for($i=1;$i<count($target->data);$i++)
                            {
                                $desctext=$desctext.", ".$this->getClassNameById($target->data[$i]);
                                //add other tahun if exist
                            }
                        }
                    //add text
                        
                    }//else the target is not array,the target is to all tahun
                    else{
                        $desctext=$desctext. '<span style="text-align: left;">Kepada Tahun Semua. ';
                    }
                    $desctext=$desctext.'<hr>'.$row->desc.'</span></div>';
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

                $table->rawColumns(['status', 'action','desctext']); //to let these 3 become html code ,not string
                
                return $table->make(true);//return datatable
        }
    }

    public function getProductNumOfGroup(Request $request){ //get product number when admin is deleting a type
        
        $productNum = DB::table('product_item as p')
        ->join('product_group as pg', 'pg.id', '=', 'p.product_group_id')
        ->select('p.name')
        ->where('pg.id',$request->groupId)
        ->whereNull('p.deleted_at')
        ->distinct('p.id')
        ->get();

        return response()->json(['productNum' => $productNum]);
        
    }

    public function deleteType(Int $id) //deleted_at to soft delete ,dont remove directly from db
    {
        $delete=DB::table('product_group')->where('id',$id);
        $org=$delete->first()->organization_id;
        $delete->update([
            'deleted_at' => now(),   
        ]);
        $removeType = DB::table('product_item')->where('product_group_id',$id)->update([
            'deleted_at'=>now(),
            'status'=>0,
        ]);
       
        return redirect('koperasi/produktype')->with([
            'success' => 'Produk type berjaya dipadam',
            'koopId' => $org,
            ]);
       
    }

    public function editType(Int $id)
    {
        $edit = DB::table('product_group')->where('id',$id)->first();

        return view('koperasi-admin.editType')->with('edit',$edit);
    }

    public function updateType(Int $id,Request $request)
    {    
      
        $update = DB::table('product_group')->where('id',$id);
        $org=$update->first()->organization_id;
        $update->update([
            'name' => $request->name,  
            'updated_at'=>now()        
        ]);
        return redirect('koperasi/produktype')->with([
            'success' => 'Produk type berjaya diubah.',
            'koopId' => $org,
            ]);
        
    }

    public function createType(Request $request)
    {
        $kid=$request->koopId;
        if($kid==null){
            $kid= $request->session()->get('koopId');
        }
        $userID = Auth::id();
       
        $org = DB::table('organizations as o')
        ->join('organization_user as os', 'o.id', 'os.organization_id')
        ->where('os.organization_id', $kid)
        ->where('os.user_id',$userID)
        ->where('o.type_org',10)
        ->select('o.id','o.nama')
        ->first();
        /*
        $type = DB::table('product_group as p')
        ->where('p.organization_id',$org->id)
        ->get();*/ //mingxuan :not sure what is it but i just comment it

        $group = DB::table('product_group as pg')
        ->join('organization_user as ou', 'pg.organization_id', '=', 'ou.organization_id')
        ->where('ou.user_id', $userID)
        ->where ('ou.organization_id',$kid)
        ->whereNull('pg.deleted_at')
        ->select('pg.*')
        ->distinct('pg.id')
        ->orderBY('pg.name')
        ->get();

        return view('koperasi-admin.addtype',compact('group','org'));
    }

    public function storeType(Request $request) //insert type in database
    {
        $userID = Auth::id();
        $org = $request->koopId;

       $add = DB::table('product_group') -> insert([
        'name' => $request->input('name'), 
        'organization_id' =>$org,
        'created_at'=>now(),
        'updated_at'=>now(),
       ]);
      
       return redirect('koperasi/produktype')->with([
        'success' => 'Produk type berjaya ditambah.',
        'koopId' => $org,
        ]);
    //    return redirect('koperasi/produktype')->with('success','Produk type berjaya ditambah.');
    }

    public function createProduct(Request $request) //show add product view
    {
        $userID = Auth::id();
        $koopId=$request->koopId;
        
        $type = DB::table('product_group as p')
        ->where('p.organization_id',$koopId)
        ->whereNull('p.deleted_at')
        ->get();
        if(count($type)==0)
        {
            return redirect('koperasi/produkmenu')->with('success','Sila tambah produk type dahulu'); //use success session only
        }
        return view('koperasi-admin.add',compact('type','koopId'));
    }

    public function storeProduct(Request $request) //insert item in database
    {
        $link = explode(" ", $request->nama);
        $str = implode("-", $link);
        
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
        
        if($request->cb_year){
            if($request->classCheckBoxEmpty==="true"){
                $data = array(
                    'data' =>$request->cb_class
                );
            }
            else{
                $data = array(
                    'data' =>$request->cb_year
                );
            }
            
            
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
        return redirect('koperasi/produkmenu')->with([
            'success' => 'Product created successfully.',
            'koopId' => $request->koopId,
            ]);
       //return redirect('koperasi/produkmenu')->with('success','Product created successfully.');
    }

    public function editProduct(Int $id)// show edit view
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
                ->whereNull('p.deleted_at')
                ->get();
  
        return view('koperasi-admin.edit',compact('type'),compact('test'))
        ->with('test',$test)
        ->with('edit',$edit);
    }

    public function updateProduct(Request $request,Int $id)//update product in database
    {
        $userID = Auth::id();
       

        if($request->cb_year){
            if($request->classCheckBoxEmpty==="true"){
                $data = array(
                    'data' =>$request->cb_class
                );
            }
            else{
                $data = array(
                    'data' =>$request->cb_year
                );
            }           
        }
        else if ($request->year==="Default"){
            $data=json_decode(DB::table('product_item')->where('id',$id)->first()->target);
            
        }
        else{
            $data=['data' => 'All'];
        }
        $target = json_encode($data);

        $update = DB::table('product_item')->where('id',$id)->update([
            'name' => $request->nama,
            'desc' => $request->description,
            'image' => $request->image,
            'quantity_available' => $request->quantity,
            'price' => $request->price,
            'status'=> $request->status,
            'target'=>$target,
            'product_group_id' => $request->type,
            'updated_at' => now()
        ]);
        if($request->quantity ==0)
        {
            DB::table('product_item')->where('id',$id)->update(['status'=> 0]);
        }

        $koopId = DB::table('organizations as o')
        ->join('organization_user as os', 'o.id', 'os.organization_id')
        ->join('product_group as pg','pg.organization_id','o.id')
        ->where('pg.id', $request->type)
        ->where('os.user_id', $userID)
        ->where('o.type_org',10)
        ->select('o.id')
        ->first();
       
        return redirect('koperasi/produkmenu')->with([
            'success' => 'Product updated successfully.',
            'koopId' => $koopId->id,
            ]);
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

        }//manage return message
        return redirect('koperasi/produkmenu')->with('success',$returnInformation);
    }

    public function fetchClassyear(Request $request){
        
        
        $userID = Auth::id();
        $oid = DB::table('organizations as o')
                ->join('organization_user as os', 'o.id', 'os.organization_id')
                ->where('os.user_id', $userID)
                ->where('o.type_org',10)
                ->select('o.parent_org')
                ->first();
        if($oid==null){
            $oid = DB::table('organizations as o')
            ->join('organization_user as os', 'o.id', 'os.organization_id')
            ->where('o.id',$request->koopId)
            ->where('o.type_org',10)
            ->select('o.parent_org')
            ->first();
        }

        
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

    public function getClassNameById($id){
        $class = DB::table('classes as c')
        ->where('c.id',$id)
        ->select('c.nama as classname')
        ->first();
        if ($class) {
            return $class->classname;
        }
    
        return "";       
    }

    public function importproducttype(Request $request){
        
        $file       = $request->file('file');
        $namaFile   = $file->getClientOriginalName();
        $file->move('uploads/excel/', $namaFile);
        
        $etx = $file->getClientOriginalExtension();
        $formats = ['xls', 'xlsx', 'ods', 'csv'];
        
        if (!in_array($etx, $formats)) {

            return redirect('koperasi/produktype')->withErrors(['format' => 'Only supports upload .xlsx, .xls files']);
        }
        
        Excel::import(new ProductTypeImport($request->organ), public_path('/uploads/excel/' . $namaFile));
        
        return redirect('koperasi/produktype')->with([
            'success' => 'Produk type berjaya ditambah.',
            'koopId' => $request->organ,
            ]);
       
        
    
    }

    public function importproduct(Request $request){
        $file       = $request->file('file');
        $namaFile   = $file->getClientOriginalName();
        $file->move('uploads/excel/', $namaFile);
        
        $etx = $file->getClientOriginalExtension();
        $formats = ['xls', 'xlsx', 'ods', 'csv'];
        
        if (!in_array($etx, $formats)) {

            return redirect('koperasi/produktype')->withErrors(['format' => 'Only supports upload .xlsx, .xls files']);
        }
        if($request->cb_year){
            if($request->classCheckBoxEmpty==="true"){
                $data = array(
                    'data' =>$request->cb_class
                );
            }
            else{
                $data = array(
                    'data' =>$request->cb_year
                );
            }
            
            
        }
        else{
            $data=['data' => 'All'];
        }
        $target = json_encode($data);//to make json
        Excel::import(new ProductImport($request->type,$target,$request->organ,null), public_path('/uploads/excel/' . $namaFile));
        
        return redirect('koperasi/produkmenu')->with([
            'success' => 'Product type have been added successfully.',
            'koopId' => $request->organ,
            ]);
        //return redirect('koperasi/produkmenu')->with('success', 'Product type have been added successfully');
    }


    ///////////////testing area////////////////////////////////////////////
    // public function testing(){
    //     $transaction = Transaction::where('id', '=', 29489)->first();
    //     //dd($transaction);
    //     $list_student_fees_id = DB::table('student_fees_new')
    //     ->join('fees_transactions_new', 'fees_transactions_new.student_fees_id', '=', 'student_fees_new.id')
    //     ->join('transactions', 'transactions.id', '=', 'fees_transactions_new.transactions_id')
    //     ->select('student_fees_new.id as student_fees_id', 'student_fees_new.class_student_id')
    //     ->where('transactions.id', $transaction->id)
    //     ->get();

    //     $list_parent_fees_id  = DB::table('fees_new')
    //         ->join('fees_new_organization_user', 'fees_new_organization_user.fees_new_id', '=', 'fees_new.id')
    //         ->join('organization_user', 'organization_user.id', '=', 'fees_new_organization_user.organization_user_id')
    //         ->select('fees_new_organization_user.*')
    //         ->orderBy('fees_new.category')
    //         ->where('organization_user.user_id', $transaction->user_id)
    //         ->where('organization_user.role_id', 6)
    //         ->where('organization_user.status', 1)
    //         ->where('fees_new_organization_user.transaction_id', $transaction->id)
    //         ->get();
    //     dd($list_parent_fees_id ,$list_student_fees_id);

    // for ($i = 0; $i < count($list_student_fees_id); $i++) {

    //     // ************************* update student fees status fees by transactions *************************
    //     $res  = DB::table('student_fees_new')
    //         ->where('id', $list_student_fees_id[$i]->student_fees_id)
    //         ->update(['status' => 'Paid']);

    //     // ************************* check the student if have still debt *************************
        
    //     if ($i == count($list_student_fees_id) - 1)
    //     {
    //         $check_debt = DB::table('students')
    //             ->join('class_student', 'class_student.student_id', '=', 'students.id')
    //             ->join('student_fees_new', 'student_fees_new.class_student_id', '=', 'class_student.id')
    //             ->select('students.*')
    //             ->where('class_student.id', $list_student_fees_id[$i]->class_student_id)
    //             ->where('student_fees_new.status', 'Debt')
    //             ->count();


    //         // ************************* update status fees for student if all fees completed paid*************************

    //         if ($check_debt == 0) {
    //             DB::table('class_student')
    //                 ->where('id', $list_student_fees_id[$i]->class_student_id)
    //                 ->update(['fees_status' => 'Completed']);
    //         }
    //     }
    // }

    // for ($i = 0; $i < count($list_parent_fees_id); $i++) {

    //     // ************************* update status fees for parent *************************
    //     DB::table('fees_new_organization_user')
    //         ->where('id', $list_parent_fees_id[$i]->id)
    //         ->update([
    //             'status' => 'Paid'
    //         ]);
            
    //     // ************************* check the parent if have still debt *************************
    //     if ($i == count($list_parent_fees_id) - 1)
    //     {
    //         $check_debt = DB::table('organization_user')
    //             ->join('fees_new_organization_user', 'fees_new_organization_user.organization_user_id', '=', 'organization_user.id')
    //             ->where('organization_user.user_id', $transaction->user_id)
    //             ->where('organization_user.role_id', 6)
    //             ->where('organization_user.status', 1)
    //             ->where('fees_new_organization_user.status', 'Debt')
    //             ->count();

    //         // ************************* update status fees for organization user (parent) if all fees completed paid *************************

    //         if ($check_debt == 0) {
    //             DB::table('organization_user')
    //                 ->where('user_id', $transaction->user_id)
    //                 ->where('role_id', 6)
    //                 ->where('status', 1)
    //                 ->update(['fees_status' => 'Completed']);
    //         }
    //     }
    // }

                    
    //                 //return $this->ReceiptFees($transaction->id);
    // }
    // public function findParentTesting(){
    //     $user=DB::table('organization_user as ou')
    //                         ->join('users as u','u.id','ou.user_id')
    //                         ->where('ou.organization_id',159)
    //                         ->where('ou.status',1)                       
    //                         ->groupBy('ou.user_id')
    //                         ->havingRaw('COUNT(ou.user_id) >= 2')
    //                         ->select('ou.user_id as uid')
    //                         ->distinct('ou.id')
    //                         ->get();
    //     $Array = [];
    //     foreach($user as $u ){
    //         $ou=DB::table('organization_user as ou')
    //             ->join('users as u','u.id','ou.user_id')
    //             ->where('u.id',$u->uid)
    //             ->where('ou.organization_id',159)
    //             ->select('ou.id')               
    //             ->get();
    //         $first=0;
    //         foreach($ou as $o){
    //             if($first++==0)
    //                 continue;
    //            DB::table('organization_user')->where('id',$o->id)->update([
    //                 'status' => 0, 
    //                 ]);

    //         }
    //         //dd("stop");
    //     }
  
    //     dd($user);
    // }
    // public function testing(){
    //     $user=DB::table('organization_user as ou')
    //                             ->join('users as u','u.id','ou.user_id')
    //                             ->where('ou.organization_id',141)
    //                             ->where('ou.role_id',6)
    //                             ->where('ou.status',1)                       
    //                             ->groupBy('u.id')
    //                             ->select('ou.user_id as uid')
    //                             ->distinct('u.id')
    //                             ->get();
        
    //     $class = DB::table('classes as c')
    //                 ->join('class_organization as co','co.class_id','c.id')
    //                 ->where('co.organization_id',141)
    //                 ->select('c.id','c.nama')
    //                 ->get();
    //     //dd($class);
    //     foreach($user as $u)
    //     {
    //         $students= DB::table('students as s')
    //                     ->join('organization_user_student as ous','s.id','ous.student_id')
    //                     ->join('organization_user as ou','ous.organization_user_id','ou.id')
    //                     ->join('users as u','u.id','ou.user_id')
    //                     ->join('class_student as cs','cs.student_id','s.id')
    //                     ->join('class_organization as co','co.id','cs.organclass_id')
    //                     ->whereIn('co.class_id',[315,316,317,318,319])
    //                     ->where('u.id',$u->uid)
    //                     ->get();
            
    //         if(count($students)==1){
    //             $result=DB::table('fees_new_organization_user as fou')
    //                     ->join('organization_user as ou','ou.id','fou.organization_user_id')
    //                     ->where('ou.user_id',$u->uid)
    //                     ->where('fou.status',"Debt")
    //                     ->where('fees_new_id',565)
    //                     ->select('fou.id')
    //                     ->get();
               
    //             dd($students,$u,$result);
    //         }
    //     }                       
    //     dd($user);
    // }
}
