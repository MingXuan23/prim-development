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
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
