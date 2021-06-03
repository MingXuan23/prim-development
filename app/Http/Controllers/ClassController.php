<?php

namespace App\Http\Controllers;

use App\Exports\ClassExport;
use App\Imports\ClassImport;
use App\Models\ClassModel;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class ClassController extends Controller
{

    public function index()
    {
        $organization = $this->getOrganizationByUserId();
        return view('class.index', compact('organization'));
    }

    public function classexport()
    {
        return Excel::download(new ClassExport, 'class.xlsx');
    }

    public function classimport(Request $request)
    {
        $file       = $request->file('file');
        $namaFile   = $file->getClientOriginalName();
        $file->move('uploads/excel/', $namaFile);

        Excel::import(new ClassImport, public_path('/uploads/excel/' . $namaFile));

        return redirect('/class')->with('success', 'New class has been added successfully');
    }

    public function create()
    {
        //
        $organization = $this->getOrganizationByUserId();
        return view('class.add', compact('organization'));
    }

    public function store(Request $request)
    {
        //
        $this->validate($request, [
            'name'          =>  'required',
            'level'         =>  'required',
            'organization'  =>  'required',
        ]);

        $class = new ClassModel([
            'nama'          =>  $request->get('name'),
            'levelid'       =>  $request->get('level'),
            'status'       =>  "1",
        ]);
        $class->save();

        DB::table('class_organization')->insert([
            'organization_id' => $request->get('organization'),
            'class_id'        => $class->id,
            'start_date'      => now(),
        ]);

        return redirect('/class')->with('success', 'New class has been added successfully');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
        $organization = $this->getOrganizationByUserId();
        $class       = DB::table('classes')
            ->join('class_organization', 'class_organization.class_id', '=', 'classes.id')
            ->where('classes.id', $id)->first();

        return view('class.update', compact('class', 'organization'));
    }

    public function update(Request $request, $id)
    {
        //
        $this->validate($request, [
            'name'          =>  'required',
            'level'         =>  'required',
            'organization'  =>  'required',
        ]);

        DB::table('classes')
            ->where('id', $id)
            ->update(
                [
                    'nama'      => $request->get('name'),
                    'levelid'   => $request->get('level')
                ]
            );

        DB::table('class_organization')->where('class_id', $id)
            ->update([
                'organization_id' => $request->get('organization'),
            ]);

        return redirect('/class')->with('success', 'The data has been updated!');
    }

    public function destroy($id)
    {
        $result = DB::table('classes')
            ->join('class_organization', 'class_organization.class_id', '=', 'classes.id')
            ->where('classes.id', $id)
            ->update([
                'classes.status' => "0",
            ]);

        if ($result) {
            Session::flash('success', 'Kelas Berjaya Dipadam');
            return View::make('layouts/flash-messages');
        } else {
            Session::flash('error', 'Kelas Gagal Dipadam');
            return View::make('layouts/flash-messages');
        }
    }

    public function getClassesDatatable(Request $request)
    {
        // dd($request->oid);

        if (request()->ajax()) {
            $oid = $request->oid;

            $hasOrganizaton = $request->hasOrganization;

            $userId = Auth::id();

            if ($oid != '' && !is_null($hasOrganizaton)) {

                $data = DB::table('classes')
                    ->join('class_organization', 'class_organization.class_id', '=', 'classes.id')
                    ->select('classes.id as cid', 'classes.nama as cnama', 'classes.levelid')
                    ->where([
                        ['class_organization.organization_id', $oid],
                        ['classes.status', "1"]
                    ])
                    ->orderBy('classes.nama')
                    ->orderBy('classes.levelid');
            }
            // dd($data->oid);
            $table = Datatables::of($data);

            $table->addColumn('action', function ($row) {
                $token = csrf_token();
                $btn = '<div class="d-flex justify-content-center">';
                $btn = $btn . '<a href="' . route('class.edit', $row->cid) . '" class="btn btn-primary m-1">Edit</a>';
                $btn = $btn . '<button id="' . $row->cid . '" data-token="' . $token . '" class="btn btn-danger m-1">Buang</button></div>';
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
        } else {
            // user role guru 
            return Organization::whereHas('user', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })->get();
        }
    }
}
