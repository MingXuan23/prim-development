<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class CategoryController extends Controller
{

    public function index()
    {
        //
        // get id user
        $organization = $this->getOrganizationByUserId();
        // return view('teacher.index', compact('organization'));

        // $listcategory = DB::table('categories')
        //     ->get();
        return view('category.index', compact('organization'));
    }

    public function create()
    {
        $organization = $this->getOrganizationByUserId();

        //
        return view('category.add', compact('organization'));
    }

    public function store(Request $request)
    {
        //
        $this->validate($request, [
            'name'              =>  'required',
            'description'       =>  'required',
        ]);

        $newcategory = new Category([
            'nama'              =>  $request->get('name'),
            'description'       =>  $request->get('description'),
            'organization_id'   =>  $request->get('organization')
        ]);

        $newcategory->save();

        return redirect('/category')->with('success', 'Kategori telah berjaya dimasukkan!');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
        $category = DB::table('categories')->where('id', $id)->first();
        return view('updatecategory', compact('category'));
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

    public function getCategoryDatatable(Request $request)
    {
        // dd($request->oid);

        if (request()->ajax()) {
            $oid = $request->oid;

            $hasOrganizaton = $request->hasOrganization;

            $userId = Auth::id();

            if ($oid != '') {

                $data = DB::table('categories')
                    ->join('organizations', 'organizations.id', '=', 'categories.organization_id')
                    ->select('categories.*')
                    ->where('organizations.id', $oid)
                    ->orderBy('categories.nama');
            }
            // dd($data->oid);
            $table = Datatables::of($data);

            $table->addColumn('action', function ($row) {
                $token = csrf_token();
                $btn = '<div class="d-flex justify-content-center">';
                // $btn = $btn . '<a href="' . route('details.getfees', $row->feeid) . '" class="btn btn-primary m-1">Butiran</a>';
                $btn = $btn . '<a href="' . route('category.getCategoryDetails', $row->id) . '" class="btn btn-primary m-1"> Butiran</a>';
                $btn = $btn . '<a href="' . route('category.edit', $row->id) . '" class="btn btn-primary m-1">Edit</a>';
                $btn = $btn . '<button id="' . $row->id . '" data-token="' . $token . '" class="btn btn-danger m-1">Buang</button></div>';
                return $btn;
            });

            $table->rawColumns(['action']);
            return $table->make(true);
        }
    }

    public function getCategoryDetails($id)
    {
        // id category
        // dd($id);
        $category = Category::where('id', $id)->first();

        // dd($category);
        return view('details.indexBycategory', compact('category'));
    }

    public function getCategoryDetailsDatatable(Request $request)
    {
        $categoryid = $request->catid;

        if (request()->ajax()) {

            if ($categoryid != '') {

                $data = DB::table('details')
                    ->join('categories', 'categories.id', '=', 'details.category_id')
                    ->select('details.*')
                    ->where('categories.id', $categoryid)
                    ->orderBy('details.nama');
            }
            // dd($data->oid);
            $table = Datatables::of($data);

            $table->addColumn('action', function ($row) {
                $token = csrf_token();
                $btn = '<div class="d-flex justify-content-center">';
                $btn = $btn . '<a href="' . route('details.edit', $row->id) . '" class="btn btn-primary m-1">Edit</a>';
                $btn = $btn . '<button id="' . $row->id . '" data-token="' . $token . '" class="btn btn-danger m-1">Buang</button></div>';
                return $btn;
            });

            $table->rawColumns(['action']);
            return $table->make(true);
        }
    }

    public function getOrganizationByUserId()
    {
        $userId = Auth::id();
        if (Auth::user()->hasRole('Superadmin')) {

            return Organization::all();
        } else if (Auth::user()->hasRole('Pentadbir') || Auth::user()->hasRole('Guru')) {

            // user role pentadbir n guru 
            return Organization::whereHas('user', function ($query) use ($userId) {
                $query->where('user_id', $userId)->Where(function ($query) {
                    $query->where('organization_user.role_id', '=', 4)
                        ->Orwhere('organization_user.role_id', '=', 5);
                });
            })->get();
        } else {
            // user role ibu bapa
            return Organization::whereHas('user', function ($query) use ($userId) {
                $query->where('user_id', $userId)->where('role_id', '6')->OrWhere('role_id', '7')->OrWhere('role_id', '8');
            })->get();
        }
    }

    public function getDetails(Request $request)
    {

        $categoryid = $request->get('cid');

        $list = DB::table('details')
            ->where('category_id', $categoryid)
            ->orderBy('details.nama')
            ->get();

        return response()->json(['categorylist' => $list]);
    }
}
