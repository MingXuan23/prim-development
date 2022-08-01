<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use function PHPSTORM_META\type;

class CooperativeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function indexAdmin( )
    {
        $userID = Auth::id();
        $koperasi = DB::table('organizations as o')
                    ->join('organization_user as ou', 'o.id', '=', 'ou.organization_id')
                    ->where('ou.user_id', $userID)
                    ->where('ou.role_id', 1239)
                    ->first();

        $product = DB::table('product_item as p')
                    ->where('p.organization_id',$userID)
                    ->get();
                    
        return view('koperasi-admin.index', compact('koperasi'))
        ->with('product',$product);
    }

    public function createProduct()
    {
        $type = DB::table('product_type')->get();
        return view('koperasi-admin.add',compact('type'));
    }

    public function storeProduct(Request $request)
    {
       $add = DB::table('product_item') -> insert([
        'name' => $request->input('nama'),
        'desc' => $request->input('description'),
        'image' => $request->input('image'),
        'quantity' => $request->input('quantity'),
        'price' => $request->input('price'),
        'status'=> $request->input('status'),
        'product_type_id' => $request ->input('type'),
        'organization_id' => 5,     
       ]);

    //    $add = DB::table('product_item');
    //    if($request->hasfile('image'))
    //    {
    //        $request -> file('image')->move('photo/',$request->file('image')->getClientOriginalName());
    //        $add->image =$request->file('image')->getClientOriginalName();
    //        $add -> upsert(['image' => $request->input('image')]);
    //    }
       return redirect('koperasi/admin')->with('success','Product created successfully.');
    }

    public function editProduct(Int $id)
    {
        $test = DB::table('product_item as p')
        ->join('product_type as pt', 'p.product_type_id', '=', 'pt.id')
        ->select('p.*', 'pt.name as type_name')
        ->get()
        ->where('id',$id)
        ->first();
        $edit = DB::table('product_item')->where('id',$id)->first();
        $type = DB::table('product_type')->get();
        return view('koperasi-admin.edit',compact('type'),compact('test'))
        ->with('test',$test)
        ->with('edit',$edit);
    }

    public function updateProduct(Request $request,Int $id)
    {
        $update = DB::table('product_item')->where('id',$id)->update([
            'name' => $request->nama,
            'desc' => $request->description,
            'image' => $request->image,
            'quantity' => $request->quantity,
            'price' => $request->price,
            'status'=> $request->status,
            'product_type_id' => $request->type,
            'organization_id' => 5,
          
        ]);
        if($request->quantity ==0)
        {
            DB::table('product_item')->where('id',$id)->update(['status'=> 0]);
        }
        return redirect('koperasi/admin')->with('success','Product updated successfully.');
    }


    public function deleteProduct(Int $id)
    {
        $delete = DB::table('product_item')->where('id',$id)->delete();
        return redirect('koperasi/admin')->with('success','Product deleted successfully.');

    }
}
