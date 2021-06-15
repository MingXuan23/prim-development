<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{

    public function index()
    {
        //
        // get id user
        $listcategory = DB::table('categories')
            ->get();
        return view('pentadbir.fee.indexcategory', compact('listcategory'));
    }

    public function create()
    {
        //
        return view('pentadbir.fee.addcategory');
    }

    public function store(Request $request)
    {
        //
        $this->validate($request, [
            'name'          =>  'required',
        ]);

        DB::table('categories')->insert([
            'nama' => $request->get('name')
        ]);

        return redirect('/category')->with('success', 'New category has been added successfully');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
        $category = DB::table('categories')->where('id', $id)->first();
        return view('pentadbir.fee.updatecategory', compact('category'));
    }

    public function update(Request $request, $id)
    {
        //
        $this->validate($request, [
            'name'         =>  'required',
        ]);

        $kategoriupdate    = DB::table('categories')
            ->where('id', $id)
            ->update(
                [
                    'nama'      => $request->get('name'),
                ]
            );

        return redirect('/fees')->with('success', 'Data kategori telah dikemaskini!');
    }

    public function destroy($id)
    {
        //
    }
}
